<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['reviews' => $request->user()->reviews()->with('product')->latest()->paginate(10)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['order_item_id' => ['required', 'integer', 'exists:order_items,id'], 'rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['nullable', 'string', 'max:3000']]);
        $item = OrderItem::query()->whereKey($data['order_item_id'])->whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id)->where('status', 'selesai'))->firstOrFail();

        if (Review::query()->where('user_id', $request->user()->id)->where('order_item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Produk dari pesanan ini sudah pernah diulas.'], 422);
        }

        $review = $request->user()->reviews()->create([
            'product_id' => $item->product_id,
            'order_item_id' => $item->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json(['message' => 'Ulasan berhasil disimpan.', 'review' => $review->load('product')], 201);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        abort_unless($review->user_id === $request->user()->id, 404);
        $review->update($request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['nullable', 'string', 'max:3000']]));

        return response()->json(['message' => 'Ulasan diperbarui.', 'review' => $review->fresh('product')]);
    }

    public function destroy(Request $request, Review $review): JsonResponse
    {
        abort_unless($review->user_id === $request->user()->id, 404);
        $review->delete();

        return response()->json(['message' => 'Ulasan dihapus.']);
    }
}
