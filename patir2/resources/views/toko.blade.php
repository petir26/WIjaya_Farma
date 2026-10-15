<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Toko | Wijaya Farma</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page toko-page">
    <div class="beranda-screen">
        <header class="beranda-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16">
                        <path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" />
                    </svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16">
                        <path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" />
                        <circle cx="10" cy="14" r=".8" />
                    </svg>
                    <svg class="battery-icon" viewBox="0 0 28 16">
                        <rect x="1" y="1.5" width="23" height="13" rx="3.5" />
                        <path d="M26 5.5v5" />
                    </svg>
                </span>
            </div>

            <h1>Toko</h1>

            <form class="store-search" role="search" onsubmit="return false">
                <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="7.2" />
                    <path d="m16.2 16.2 5 5" />
                </svg>
                <label class="visually-hidden" for="product-search">Cari obat atau kategori</label>
                <input id="product-search" type="search" value="{{ request('kategori') }}" placeholder="Cari obat atau kategori..." autocomplete="off">
            </form>
            <button class="category-toggle" id="category-toggle" type="button" aria-label="Pilih kategori obat" aria-expanded="false" aria-controls="category-panel">
                <svg class="category-list-icon" viewBox="0 0 28 28" aria-hidden="true">
                    <circle cx="4" cy="6" r="2" /><circle cx="4" cy="14" r="2" /><circle cx="4" cy="22" r="2" />
                    <path d="M10 6h15M10 14h15M10 22h15" />
                </svg>
            </button>
        </header>

        <main class="beranda-content" aria-label="Daftar obat">
            <div class="product-grid" id="product-grid">
                @foreach ($products as $product)
                    <article
                        class="product-card"
                        data-product-card
                        data-search="{{ \Illuminate\Support\Str::lower($product['name'].' '.$product['category'].' '.$product['package'].' '.$product['description']) }}"
                    >
                        <div class="product-image product-image--{{ \Illuminate\Support\Str::slug($product['name']) }}">
                            <img src="{{ asset('images/'.$product['image']) }}" alt="{{ $product['name'] }}" @if ($loop->index > 1) loading="lazy" @endif>
                        </div>

                        <div class="product-details">
                            <span class="product-category">{{ $product['category'] }}</span>
                            <h2 class="product-name" title="{{ $product['name'] }}">{{ $product['display_name'] }}</h2>
                            <p class="product-package">{{ $product['package'] }}</p>
                            <p class="product-price">{{ $product['price'] }}</p>
                            <p class="product-stock">Stok: {{ $product['stock'] }}</p>

                            <div class="product-actions">
                                <a class="product-info" href="{{ route('informasi-obat.show', \Illuminate\Support\Str::slug($product['name'])) }}">Informasi</a>
                                <button class="product-buy" type="button" data-add-to-cart data-product-id="{{ $product['id'] }}" @disabled($product['stock'] < 1)>Beli</button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <p class="empty-results" id="empty-results" hidden>Obat tidak ditemukan.</p>
        </main>

        <section class="category-panel" id="category-panel" aria-label="Pilih kategori obat" hidden>
            <div class="category-panel-heading">
                <h2>Kategori Obat</h2>
                <button class="category-panel-close" id="category-panel-close" type="button" aria-label="Tutup kategori">&times;</button>
            </div>
            <div class="category-panel-grid">
                <button class="category-option" type="button" data-category-query="">
                    <span class="category-option-icon all-categories-icon" aria-hidden="true">✦</span>
                    <span>Semua</span>
                </button>
                <button class="category-option" type="button" data-category-query="vitamin">
                    <img class="category-option-icon" src="{{ asset('images/cat-vitamin.png') }}" alt="">
                    <span>Vitamin</span>
                </button>
                <button class="category-option" type="button" data-category-query="demam">
                    <img class="category-option-icon" src="{{ asset('images/cat-demam.png') }}" alt="">
                    <span>Demam</span>
                </button>
                <button class="category-option" type="button" data-category-query="flu">
                    <img class="category-option-icon" src="{{ asset('images/cat-flu.png') }}" alt="">
                    <span>Flu</span>
                </button>
                <button class="category-option" type="button" data-category-query="batuk">
                    <img class="category-option-icon" src="{{ asset('images/cat-batuk.png') }}" alt="">
                    <span>Batuk</span>
                </button>
                <button class="category-option" type="button" data-category-query="herbal">
                    <img class="category-option-icon" src="{{ asset('images/cat-herbal.png') }}" alt="">
                    <span>Herbal</span>
                </button>
                <button class="category-option" type="button" data-category-query="gigi">
                    <img class="category-option-icon" src="{{ asset('images/cat-gigi.png') }}" alt="">
                    <span>Sakit Gigi</span>
                </button>
                <button class="category-option" type="button" data-category-query="kepala">
                    <img class="category-option-icon" src="{{ asset('images/cat-kepala.png') }}" alt="">
                    <span>Sakit Kepala</span>
                </button>
                <button class="category-option" type="button" data-category-query="lambung">
                    <img class="category-option-icon" src="{{ asset('images/cat-lambung.png') }}" alt="">
                    <span>Lambung</span>
                </button>
            </div>
        </section>

        <button class="floating-cart" id="floating-cart" type="button" aria-label="Buka keranjang" hidden>
            <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9"/><circle cx="15" cy="33" r="2.8"/><circle cx="31" cy="33" r="2.8"/></svg>
            <span id="floating-cart-count">0</span>
        </button>
        <p class="visually-hidden" id="cart-status" role="status" aria-live="polite"></p>

        <nav class="beranda-bottom-nav" aria-label="Navigasi utama">
            <a class="nav-tab" href="{{ route('beranda') }}">
                <svg viewBox="0 0 32 32" aria-hidden="true">
                    <path d="m3 14 13-11 13 11v14H19v-9h-6v9H3z" />
                </svg>
                <span>Beranda</span>
            </a>
            <a class="nav-tab active" href="{{ route('checkout') }}" aria-current="page">
                <svg viewBox="0 0 40 40" aria-hidden="true">
                    <path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9" />
                    <circle cx="15" cy="33" r="2.8" />
                    <circle cx="31" cy="33" r="2.8" />
                </svg>
                <span>Toko</span>
            </a>
            <a class="nav-tab" href="{{ route('konsultasi') }}">
                <svg viewBox="0 0 36 36" aria-hidden="true">
                    <path d="M5 5h26v21H15l-9 7z" />
                </svg>
                <span>Konsultasi</span>
            </a>
            <a class="nav-tab" href="{{ route('riwayat') }}">
                <svg viewBox="0 0 36 36" aria-hidden="true">
                    <path d="M18 4C9.7 4 3 6.8 3 10.2v21.3c4.3-2.6 9.4-3.9 15-3.9s10.7 1.3 15 3.9V10.2C33 6.8 26.3 4 18 4Z" />
                    <path d="M18 5v23" />
                </svg>
                <span>Riwayat</span>
            </a>
            <a class="nav-tab" href="{{ route('profil') }}">
                <svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true">
                    <circle cx="18" cy="11" r="6" />
                    <path d="M5 32a13 13 0 0 1 26 0z" />
                </svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <script>
        const productSearch = document.querySelector('#product-search');
        const productCards = [...document.querySelectorAll('[data-product-card]')];
        const emptyResults = document.querySelector('#empty-results');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const cartStatus = document.querySelector('#cart-status');
        const renderCart = async () => {
            const response = await fetch('{{ route('client.cart.index') }}', { headers: { 'Accept': 'application/json' } });
            if (!response.ok) { document.querySelector('#floating-cart').hidden = true; return; }
            const result = await response.json();
            const cartCount = result.items.reduce((sum, item) => sum + Number(item.quantity), 0);
            document.querySelector('#floating-cart-count').textContent = cartCount;
            document.querySelector('#floating-cart').hidden = cartCount === 0;
        };
        const openCheckout = () => { window.location.href = '{{ route('checkout') }}'; };
        document.querySelector('#floating-cart').addEventListener('click', openCheckout);
        document.querySelectorAll('[data-add-to-cart]').forEach((button) => button.addEventListener('click', async () => {
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest
            button.disabled = true;
            try {
                const response = await fetch('{{ route('client.cart.store') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ product_id: Number(button.dataset.productId), quantity: 1 }),
                });
                const result = await response.json();
                cartStatus.textContent = response.ok ? result.message : (result.message || 'Produk belum bisa ditambahkan.');
                if (response.ok) await renderCart();
            } catch (error) {
                cartStatus.textContent = 'Gagal menghubungi server. Coba lagi.';
            } finally {
                button.disabled = false;
            }
        }));
        renderCart().catch(() => {});
        const categoryToggle = document.querySelector('#category-toggle');
        const categoryPanel = document.querySelector('#category-panel');
        const categorySearch = new URLSearchParams(window.location.search).get('kategori');

        const filterProducts = () => {
            const query = productSearch.value.trim().toLocaleLowerCase();
            let visibleCount = 0;

            productCards.forEach((card) => {
                const matches = card.dataset.search.includes(query);
                card.hidden = !matches;
                visibleCount += Number(matches);
            });

            emptyResults.hidden = visibleCount > 0;
        };

        productSearch.addEventListener('input', filterProducts);

        if (categorySearch) {
            productSearch.value = categorySearch;
            filterProducts();
        }

        const setCategoryPanelOpen = (isOpen) => {
            categoryPanel.hidden = !isOpen;
            categoryToggle.setAttribute('aria-expanded', String(isOpen));
        };

        categoryToggle.addEventListener('click', () => {
            setCategoryPanelOpen(categoryPanel.hidden);
        });

        document.querySelector('#category-panel-close').addEventListener('click', () => {
            setCategoryPanelOpen(false);
        });

        document.querySelectorAll('[data-category-query]').forEach((option) => {
            option.addEventListener('click', () => {
                productSearch.value = option.dataset.categoryQuery;
                filterProducts();
                setCategoryPanelOpen(false);
            });
        });

    </script>
</body>
</html>

