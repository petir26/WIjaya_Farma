<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check Out | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page checkout-page">
    <div class="beranda-screen">
        <header class="beranda-header">
            <div class="status-bar" aria-label="Waktu 9:41"><span>9:41</span><span class="status-icons" aria-hidden="true">
                <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
            </span></div>
            <h1>Check Out</h1>
            <button class="category-toggle" type="button" aria-label="Kategori obat"><svg class="category-list-icon" viewBox="0 0 28 28" aria-hidden="true"><circle cx="4" cy="6" r="2"/><circle cx="4" cy="14" r="2"/><circle cx="4" cy="22" r="2"/><path d="M10 6h15M10 14h15M10 22h15"/></svg></button>
            <div class="store-search" role="search"><svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="7.2"/><path d="m16.2 16.2 5 5"/></svg><span>Cari obat atau kategori...</span></div>
        </header>

        <main class="checkout-content" aria-label="Pesanan di keranjang">
            <div class="checkout-grid" id="checkout-grid"></div>
            <p class="checkout-empty-page" id="checkout-empty-page" hidden>Keranjangmu masih kosong.</p>
        </main>

        <a class="checkout-back" href="{{ route('toko') }}" aria-label="Kembali ke toko"><span aria-hidden="true">‹</span></a>
        <a class="floating-cart checkout-floating-cart" href="{{ route('checkout.confirmation') }}" aria-label="Lanjutkan ke konfirmasi checkout">
            <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9"/><circle cx="15" cy="33" r="2.8"/><circle cx="31" cy="33" r="2.8"/></svg>
            <span id="checkout-cart-count">0</span>
        </a>

        <nav class="beranda-bottom-nav" aria-label="Navigasi utama">
            <a class="nav-tab" href="{{ route('beranda') }}"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 14 13-11 13 11v14H19v-9h-6v9H3z"/></svg><span>Beranda</span></a>
            <a class="nav-tab active" href="{{ route('checkout') }}" aria-current="page"><svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9"/><circle cx="15" cy="33" r="2.8"/><circle cx="31" cy="33" r="2.8"/></svg><span>Toko</span></a>
            <a class="nav-tab" href="{{ route('konsultasi') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z"/></svg><span>Konsultasi</span></a>
            <a class="nav-tab" href="{{ route('riwayat') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><path d="M3 6c6-2 11-1 15 2 4-3 9-4 15-2v24c-6-2-11-1-15 2-4-3-9-4-15-2zM18 8v24"/></svg><span>Riwayat</span></a>
            <a class="nav-tab" href="{{ route('profil') }}"><svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6"/><path d="M5 32a13 13 0 0 1 26 0z"/></svg><span>Profil</span></a>
        </nav>
    </div>

    <dialog class="delete-dialog" id="delete-dialog" aria-labelledby="delete-title">
        <h2 id="delete-title">Hapus pesanan?</h2><p>Pesanan ini akan dihapus dari keranjang.</p>
        <div><button type="button" data-cancel-delete>Batal</button><button class="delete-confirm" type="button" id="confirm-delete">Hapus</button></div>
    </dialog>

    <script>
        const grid = document.querySelector('#checkout-grid');
        const dialog = document.querySelector('#delete-dialog');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let cart = [];
        let pendingDeleteId = null;
        const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char]);
        const renderCart = async () => {
            const response = await fetch('{{ route('client.cart.index') }}', { headers: { 'Accept': 'application/json' } });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Keranjang belum dapat dimuat.');
            cart = result.items;
            const count = cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            document.querySelector('#checkout-cart-count').textContent = count;
            document.querySelector('#checkout-empty-page').hidden = cart.length > 0;
            document.querySelector('.checkout-floating-cart').setAttribute('aria-disabled', String(count === 0));
            grid.innerHTML = cart.map((item) => `
                <article class="checkout-product-card">
                    <div class="checkout-product-image"><img src="{{ asset('images') }}/${escapeHtml(item.product.image || '')}" alt="${escapeHtml(item.product.name)}"></div>
                    <div class="checkout-product-info">
                        <span class="product-category">${escapeHtml(item.product.category)}</span>
                        <h2>${escapeHtml(item.product.name)}</h2><p>${escapeHtml(item.product.package)}</p>
                        <strong class="checkout-product-price">Rp ${new Intl.NumberFormat('id-ID').format(Number(item.product.price))}</strong>
                        <div class="checkout-quantity">
                            <button type="button" data-action="minus" data-id="${escapeHtml(item.id)}" aria-label="Kurangi ${escapeHtml(item.product.name)}">−</button>
                            <span>${Number(item.quantity)}</span>
                            <button type="button" data-action="plus" data-id="${escapeHtml(item.id)}" aria-label="Tambah ${escapeHtml(item.product.name)}">+</button>
                        </div>
                        ${item.product.requires_prescription ? `<label class="checkout-prescription-label">Resep dokter <input type="file" accept=".jpg,.jpeg,.png,.pdf" data-prescription-product="${escapeHtml(item.product.id)}"></label><span class="checkout-prescription-status" data-prescription-status="${escapeHtml(item.product.id)}"></span>` : ''}
                        <button class="checkout-remove" type="button" data-action="remove" data-id="${escapeHtml(item.id)}">Hapus</button>
                    </div>
                </article>`).join('');
        };
        grid.addEventListener('click', async (event) => {
            const button = event.target.closest('[data-action]');
            if (!button) return;
            const item = cart.find((entry) => String(entry.id) === button.dataset.id);
            if (!item) return;
            if (button.dataset.action === 'remove') { pendingDeleteId = item.id; dialog.showModal(); return; }
            const quantity = Math.max(1, Number(item.quantity) + (button.dataset.action === 'plus' ? 1 : -1));
            await fetch(`{{ url('/client/cart') }}/${item.id}`, { method: 'PATCH', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ quantity }) });
            await renderCart();
        });
        grid.addEventListener('change', async (event) => {
            const input = event.target.closest('[data-prescription-product]');
            if (!input || !input.files?.[0]) return;
            const formData = new FormData();
            formData.append('product_id', input.dataset.prescriptionProduct);
            formData.append('file', input.files[0]);
            const status = grid.querySelector(`[data-prescription-status="${input.dataset.prescriptionProduct}"]`);
            try {
                const response = await fetch('{{ route('client.prescriptions.store') }}', { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: formData });
                const result = await response.json();
                status.textContent = response.ok ? 'Resep tersimpan' : (result.message || 'Unggah resep gagal');
            } catch (error) {
                status.textContent = 'Gagal menghubungi server.';
            }
        });
        document.querySelector('[data-cancel-delete]').addEventListener('click', () => dialog.close());
        document.querySelector('#confirm-delete').addEventListener('click', async () => {
            await fetch(`{{ url('/client/cart') }}/${pendingDeleteId}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            pendingDeleteId = null;
            await renderCart(); dialog.close();
        });
        renderCart().catch(() => { document.querySelector('#checkout-empty-page').hidden = false; });
    </script>
</body>
</html>
