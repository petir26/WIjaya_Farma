<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.toko', ['products' => Product::query()->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product(), 'heading' => 'Tambah Obat']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['dose'] = $this->parseDose($data['dose'] ?? null);
        $data['image'] = $request->file('image')?->store('products', 'public');
        $data['is_active'] = $request->boolean('is_active', true);

        Product::query()->create($data);

        return redirect()->route('admin.toko')->with('status', 'Obat berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', ['product' => $product, 'heading' => 'Edit Obat']);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        $data['slug'] = $this->uniqueSlug($data['name'], $product);
        $data['dose'] = $this->parseDose($data['dose'] ?? null);
        $data['is_active'] = $request->boolean('is_active');
        $previousImage = $product->image;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($previousImage);
        }

        return redirect()->route('admin.toko')->with('status', 'Perubahan obat berhasil disimpan.');
    }

    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['stock' => ['required', 'integer', 'min:0', 'max:1000000']]);
        $product->update(['stock' => $data['stock']]);

        return redirect()->route('admin.toko')->with('status', 'Stok berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return redirect()->route('admin.toko')->with('status', 'Obat dihapus dari katalog toko.');
    }

    private function validatedProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:80'],
            'package' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'description' => ['nullable', 'string', 'max:10000'],
            'content' => ['nullable', 'string', 'max:150'],
            'dose' => ['nullable', 'string', 'max:5000'],
            'warning' => ['nullable', 'string', 'max:10000'],
            'requires_prescription' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function parseDose(?string $dose): ?array
    {
        if ($dose === null || trim($dose) === '') {
            return null;
        }

        return array_values(array_filter(array_map('trim', preg_split('/\R/', $dose) ?: [])));
    }

    private function uniqueSlug(string $name, ?Product $product = null): string
    {
        $base = Str::slug($name) ?: 'obat';
        $slug = $base;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->when($product, fn ($query) => $query->where('id', '!=', $product->id))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function deleteUploadedImage(?string $image): void
    {
        if ($image && str_starts_with($image, 'products/')) {
            Storage::disk('public')->delete($image);
        }
    }
}
