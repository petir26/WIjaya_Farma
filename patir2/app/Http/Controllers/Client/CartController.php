<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['items' => $request->user()->cartItems()->with('product')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('is_active', true)], 'quantity' => ['sometimes', 'integer', 'min:1']]);
        $product = Product::query()->whereKey($data['product_id'])->where('is_active', true)->firstOrFail();
        $item = CartItem::query()->firstOrNew(['user_id' => $request->user()->id, 'product_id' => $product->id]);
        $item->quantity = (int) $item->quantity + (int) ($data['quantity'] ?? 1);

        if ($item->quantity > $product->stock) {
            return response()->json(['message' => 'Jumlah pesanan melebihi stok tersedia.', 'stock' => $product->stock], 422);
        }

        $item->save();

        return response()->json(['message' => 'Produk ditambahkan ke keranjang.', 'item' => $item->load('product')], 201);
    }

    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:' . $cartItem->product->stock]]);
        $cartItem->update($data);

        return response()->json(['message' => 'Jumlah produk diperbarui.', 'item' => $cartItem->load('product')]);
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);
        $cartItem->delete();

        return response()->json(['message' => 'Produk dihapus dari keranjang.']);
    }
}
