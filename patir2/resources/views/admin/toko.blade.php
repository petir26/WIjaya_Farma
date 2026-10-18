<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Toko | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page admin-store-page">
    <div class="beranda-screen admin-store-screen">
        <header class="beranda-header admin-store-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                    <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
                </span>
            </div>
            <h1>Kelola Toko</h1>
            <div class="admin-store-tools">
                <label class="admin-store-search">
                    <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="7.2" /><path d="m16.2 16.2 5 5" /></svg>
                    <span class="visually-hidden">Cari obat atau kategori</span>
                    <input id="admin-product-search" type="search" placeholder="Cari obat atau kategori..." autocomplete="off">
                </label>
                <a class="admin-add-product" href="{{ route('admin.products.create') }}"><span aria-hidden="true">+</span> Tambah Obat</a>
            </div>
        </header>

        <main class="admin-store-content">
            @if (session('status'))
                <p class="admin-flash-status" role="status">{{ session('status') }}</p>
            @endif
            <p class="admin-products-count">{{ $products->count() }} obat dalam katalog</p>
            <div class="admin-product-list" id="admin-product-list">
                @forelse ($products as $product)
                    @php($imageUrl = $product->image ? (str_starts_with($product->image, 'products/') ? \Illuminate\Support\Facades\Storage::disk('public')->url($product->image) : asset('images/'.$product->image)) : null)
                    <article class="admin-product-row" data-admin-product data-search="{{ \Illuminate\Support\Str::lower($product->name.' '.$product->category.' '.$product->package.' '.$product->description) }}">
                        <div class="admin-product-image">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <span aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($product->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="admin-product-details">
                            <span class="admin-product-category">{{ $product->category }}</span>
                            <h2>{{ $product->name }}</h2>
                            <p>{{ $product->package }}</p>
                            @if ($product->description)
                                <p class="admin-product-description">{{ $product->description }}</p>
                            @endif
                            <strong>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</strong>
                            <span class="admin-product-stock">Stok: {{ $product->stock }} unit</span>
                            <span class="admin-product-status {{ $product->is_active ? 'is-active' : 'is-inactive' }}">{{ $product->is_active ? 'Aktif di katalog' : 'Tidak aktif' }}</span>
                            @if ($product->requires_prescription)
                                <span class="admin-product-prescription">Memerlukan resep</span>
                            @endif
                        </div>
                        <div class="admin-product-actions" aria-label="Aksi untuk {{ $product->name }}">
                            <a href="{{ route('admin.products.edit', $product) }}" aria-label="Edit {{ $product->name }}">Edit</a>
                            <button type="button" data-stock-url="{{ route('admin.products.stock', $product) }}" data-product-name="{{ $product->name }}" data-current-stock="{{ $product->stock }}">Update Stok</button>
                            @if ($product->is_active)
                                <button class="is-delete" type="button" data-delete-url="{{ route('admin.products.destroy', $product) }}" data-product-name="{{ $product->name }}">Hapus</button>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="admin-empty-products">Katalog obat masih kosong. Tambahkan obat pertama Anda.</p>
                @endforelse
            </div>
            <p class="admin-empty-products" id="admin-empty-search" hidden>Obat tidak ditemukan.</p>
        </main>

        <nav class="beranda-bottom-nav admin-bottom-nav" aria-label="Navigasi admin">
            <a class="nav-tab active" href="{{ route('admin.toko') }}" aria-current="page">
                <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9" /><circle cx="15" cy="33" r="2.8" /><circle cx="31" cy="33" r="2.8" /></svg>
                <span>Toko</span>
            </a>
            <a class="nav-tab" href="{{ route('profil') }}">
                <svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6" /><path d="M5 32a13 13 0 0 1 26 0z" /></svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <dialog class="admin-action-dialog" id="stock-dialog" aria-labelledby="stock-dialog-title">
        <form id="stock-form" method="post">
            @csrf
            @method('PATCH')
            <h2 id="stock-dialog-title">Update Stok</h2>
            <p id="stock-product-name"></p>
            <label for="stock-value">Jumlah stok akhir</label>
            <input id="stock-value" name="stock" type="number" min="0" max="1000000" step="1" required>
            <div class="admin-dialog-actions">
                <button type="button" data-close-dialog>Batal</button>
                <button class="is-primary" type="submit">Simpan Stok</button>
            </div>
        </form>
    </dialog>

    <dialog class="admin-action-dialog" id="delete-dialog" aria-labelledby="delete-dialog-title">
        <form id="delete-form" method="post">
            @csrf
            @method('DELETE')
            <h2 id="delete-dialog-title">Hapus item?</h2>
            <p id="delete-product-name"></p>
            <p>Obat tidak akan ditampilkan lagi di katalog toko. Riwayat pesanan tetap tersimpan.</p>
            <div class="admin-dialog-actions">
                <button type="button" data-close-dialog>Batal</button>
                <button class="is-danger" type="submit">Ya, Hapus</button>
            </div>
        </form>
    </dialog>

    <script>
        const searchInput = document.querySelector('#admin-product-search');
        const productRows = [...document.querySelectorAll('[data-admin-product]')];
        const emptySearch = document.querySelector('#admin-empty-search');
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLocaleLowerCase();
            let visible = 0;
            productRows.forEach((row) => {
                row.hidden = !row.dataset.search.includes(query);
                visible += Number(!row.hidden);
            });
            emptySearch.hidden = visible > 0 || productRows.length === 0;
        });

        const stockDialog = document.querySelector('#stock-dialog');
        const stockForm = document.querySelector('#stock-form');
        const stockValue = document.querySelector('#stock-value');
        document.querySelectorAll('[data-stock-url]').forEach((button) => {
            button.addEventListener('click', () => {
                stockForm.action = button.dataset.stockUrl;
                document.querySelector('#stock-product-name').textContent = button.dataset.productName;
                stockValue.value = button.dataset.currentStock;
                stockDialog.showModal();
                stockValue.focus();
            });
        });

        const deleteDialog = document.querySelector('#delete-dialog');
        const deleteForm = document.querySelector('#delete-form');
        document.querySelectorAll('[data-delete-url]').forEach((button) => {
            button.addEventListener('click', () => {
                deleteForm.action = button.dataset.deleteUrl;
                document.querySelector('#delete-product-name').textContent = `“${button.dataset.productName}” akan dihapus dari katalog.`;
                deleteDialog.showModal();
            });
        });

        document.querySelectorAll('[data-close-dialog]').forEach((button) => {
            button.addEventListener('click', () => button.closest('dialog').close());
        });
    </script>
</body>
</html>
