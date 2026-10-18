<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::user()?->role === 'admin') {
            return redirect()->route('admin.toko');
        }

        $products = Product::query()->where('is_active', true)->orderBy('name')->get()->map(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'display_name' => $product->name,
            'category' => $product->category,
            'package' => $product->package,
            'price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
            'unit_price' => (int) $product->price,
            'image' => $product->image,
            'description' => $product->description,
            'stock' => $product->stock,
        ]);

        return view('toko', ['products' => $products]);
    }

    public function showMedicine(string $obat): View|RedirectResponse
    {
        if (Auth::user()?->role === 'admin') {
            return redirect()->route('admin.toko');
        }

        $product = Product::query()->where('slug', $obat)->where('is_active', true)->firstOrFail();

        return view('informasi-obat.show', ['medicine' => [
            'name' => $product->name,
            'display_name' => $product->name,
            'category' => $product->category,
            'package' => $product->package,
            'price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
            'image' => $product->image,
            'content' => $product->content ?? $product->name,
            'dose' => $product->dose ?? [],
            'description' => $product->description,
            'warning' => $product->warning,
        ]]);
    }
}
