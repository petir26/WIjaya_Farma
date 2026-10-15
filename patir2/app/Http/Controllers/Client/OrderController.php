<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['orders' => $request->user()->orders()->with(['items', 'shipment'])->latest()->paginate(10)]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json(['order' => $order->load(['items', 'shipment'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fulfillment_method' => ['required', Rule::in(['delivery', 'pickup'])],
            'shipping_address' => ['nullable', 'string', 'max:2000'],
            'pharmacy_branch' => ['nullable', 'string', 'max:120'],
            'payment_method' => ['required', Rule::in(['OVO', 'GoPay', 'QRIS'])],
            'prescription_ids' => ['nullable', 'array'],
            'prescription_ids.*' => ['integer', 'exists:prescriptions,id'],
        ]);
        $client = $request->user();
        $address = $data['shipping_address'] ?? $client->address;

        if ($data['fulfillment_method'] === 'pickup' && blank($data['pharmacy_branch'] ?? null)) {
            return response()->json(['message' => 'Pilih cabang apotek untuk pengambilan pesanan.'], 422);
        }

        if ($data['fulfillment_method'] === 'delivery' && blank($address)) {
            return response()->json(['message' => 'Alamat pengiriman perlu dilengkapi di profil.'], 422);
        }

        $order = DB::transaction(function () use ($client, $data, $address) {
            $cartItems = $client->cartItems()->with('product')->lockForUpdate()->get();
            abort_if($cartItems->isEmpty(), 422, 'Keranjang masih kosong.');
            $prescriptions = $client->prescriptions()->whereNull('order_id')->whereIn('id', $data['prescription_ids'] ?? [])->get();
            $prescriptionProductIds = $prescriptions->pluck('product_id')->all();

            $total = 0;
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                abort_unless($product && $product->is_active, 422, 'Ada produk yang sudah tidak tersedia.');
                $product = $product->newQuery()->whereKey($product->id)->lockForUpdate()->firstOrFail();
                abort_if($cartItem->quantity > $product->stock, 422, "Stok {$product->name} tidak mencukupi.");
                abort_if($product->requires_prescription && ! in_array($product->id, $prescriptionProductIds), 422, "Resep dokter untuk {$product->name} belum diunggah.");
                $total += (float) $product->price * $cartItem->quantity;
            }

            $order = Order::create([
                'order_number' => 'WF-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'user_id' => $client->id,
                'total_amount' => $total,
                'status' => 'menunggu_pembayaran',
                'fulfillment_method' => $data['fulfillment_method'],
                'shipping_address' => $data['fulfillment_method'] === 'delivery' ? $address : null,
                'pharmacy_branch' => $data['pharmacy_branch'] ?? null,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'belum_dibayar',
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $subtotal = (float) $product->price * $cartItem->quantity;
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_package' => $product->package,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $subtotal,
                ]);
                $product->decrement('stock', $cartItem->quantity);
            }

            if ($data['fulfillment_method'] === 'delivery') {
                Shipment::create(['order_id' => $order->id, 'address' => $address, 'status' => 'menunggu_verifikasi']);
            }

            $prescriptions->each->update(['order_id' => $order->id]);

            $client->cartItems()->delete();

            return $order->load(['items', 'shipment']);
        });

        return response()->json(['message' => 'Pesanan berhasil dibuat.', 'order' => $order], 201);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if ($order->status !== 'menunggu_pembayaran') {
            return response()->json(['message' => 'Pesanan tidak dapat dibatalkan pada status ini.'], 422);
        }

        DB::transaction(function () use ($order) {
            $order->load('items.product');
            foreach ($order->items as $item) {
                if ($item->product) $item->product->increment('stock', $item->quantity);
            }
            $order->update(['status' => 'dibatalkan']);
            $order->shipment?->update(['status' => 'dibatalkan']);
        });

        return response()->json(['message' => 'Pesanan berhasil dibatalkan.', 'order' => $order->fresh(['items', 'shipment'])]);
    }
}
