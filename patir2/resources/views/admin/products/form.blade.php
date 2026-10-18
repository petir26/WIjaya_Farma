<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <title>{{ $heading }} | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page admin-product-form-page">
    <div class="beranda-screen admin-product-form-screen">
        <header class="history-header admin-form-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                    <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
                </span>
            </div>
            <a class="admin-form-back" href="{{ route('admin.toko') }}" aria-label="Kembali ke Kelola Toko">‹</a>
            <h1>{{ $heading }}</h1>
        </header>

        <main class="admin-product-form-content">
            <form class="admin-product-form" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @if ($product->exists)
                    @method('PATCH')
                @endif
                @if ($errors->any())
                    <div class="admin-form-errors" role="alert">
                        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <label for="product-name">Nama obat</label>
                <input id="product-name" name="name" type="text" maxlength="160" value="{{ old('name', $product->name) }}" required>

                <div class="admin-form-grid">
                    <div><label for="product-category">Kategori</label><input id="product-category" name="category" type="text" maxlength="80" value="{{ old('category', $product->category) }}" required></div>
                    <div><label for="product-package">Kemasan</label><input id="product-package" name="package" type="text" maxlength="100" value="{{ old('package', $product->package) }}" placeholder="Contoh: 10 tablet" required></div>
                </div>

                <div class="admin-form-grid">
                    <div><label for="product-price">Harga (Rp)</label><input id="product-price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required></div>
                    <div><label for="product-stock">Stok awal / akhir</label><input id="product-stock" name="stock" type="number" min="0" max="1000000" step="1" value="{{ old('stock', $product->stock ?? 0) }}" required></div>
                </div>

                <label for="product-description">Deskripsi</label>
                <textarea id="product-description" name="description" rows="3" maxlength="10000">{{ old('description', $product->description) }}</textarea>

                <label for="product-content">Kandungan / informasi singkat</label>
                <input id="product-content" name="content" type="text" maxlength="150" value="{{ old('content', $product->content) }}">

                <label for="product-dose">Aturan pakai <span>(satu aturan per baris)</span></label>
                <textarea id="product-dose" name="dose" rows="3">{{ old('dose', is_array($product->dose) ? implode("\n", $product->dose) : '') }}</textarea>

                <label for="product-warning">Peringatan</label>
                <textarea id="product-warning" name="warning" rows="3" maxlength="10000">{{ old('warning', $product->warning) }}</textarea>

                <label class="admin-image-field" for="product-image">Foto obat <span>JPG, PNG, atau WEBP (maks. 5 MB)</span></label>
                <input id="product-image" name="image" type="file" accept="image/jpeg,image/png,image/webp">

                <label class="admin-form-checkbox"><input type="checkbox" name="requires_prescription" value="1" @checked(old('requires_prescription', $product->requires_prescription))> Memerlukan resep dokter</label>
                <label class="admin-form-checkbox"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))> Tampilkan di katalog toko</label>

                <div class="admin-form-actions">
                    <a href="{{ route('admin.toko') }}">Batal</a>
                    <button type="submit">{{ $product->exists ? 'Simpan Perubahan' : 'Tambah Obat' }}</button>
                </div>
            </form>
        </main>

        <nav class="beranda-bottom-nav admin-bottom-nav" aria-label="Navigasi admin">
            <a class="nav-tab active" href="{{ route('admin.toko') }}"><svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9" /><circle cx="15" cy="33" r="2.8" /><circle cx="31" cy="33" r="2.8" /></svg><span>Toko</span></a>
            <a class="nav-tab" href="{{ route('profil') }}"><svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6" /><path d="M5 32a13 13 0 0 1 26 0z" /></svg><span>Profil</span></a>
        </nav>
    </div>
</body>
</html>
