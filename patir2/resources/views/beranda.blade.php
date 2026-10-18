<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <title>Beranda | Wijaya Farma</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page home-page">
    @php
        $categories = [
            ['name' => 'Vitamin', 'image' => 'cat-vitamin.png', 'query' => 'vitamin'],
            ['name' => 'Demam', 'image' => 'cat-demam.png', 'query' => 'demam'],
            ['name' => 'Flu', 'image' => 'cat-flu.png', 'query' => 'flu'],
            ['name' => 'Batuk', 'image' => 'cat-batuk.png', 'query' => 'batuk'],
            ['name' => 'Herbal', 'image' => 'cat-herbal.png', 'query' => 'herbal'],
            ['name' => 'Sakit Gigi', 'image' => 'cat-gigi.png', 'query' => 'gigi'],
            ['name' => 'Sakit Kepala', 'image' => 'cat-kepala.png', 'query' => 'kepala'],
            ['name' => 'Lambung', 'image' => 'cat-lambung.png', 'query' => 'lambung'],
        ];
    @endphp

    <div class="home-screen">
        <header class="home-header">
            <div class="home-status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="home-status-icons" aria-hidden="true">
                    <svg viewBox="0 0 24 16">
                        <path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" />
                    </svg>
                    <svg viewBox="0 0 20 16">
                        <path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" />
                        <circle cx="10" cy="14" r=".8" />
                    </svg>
                    <svg class="home-battery-icon" viewBox="0 0 28 16">
                        <rect x="1" y="1.5" width="23" height="13" rx="3.5" />
                        <path d="M26 5.5v5" />
                        <rect x="3" y="3.5" width="19" height="9" rx="2" class="battery-charge" />
                    </svg>
                </span>
            </div>
            <h1>Beranda</h1>
        </header>

        <main class="home-content">
            <a class="home-promo" href="{{ route('toko', ['kategori' => 'vitamin']) }}" aria-label="Promo hari ini: diskon 50% untuk Vitamin dan Suplemen" style="background-image: url('{{ asset('images/promo-vitamin.png') }}');">
                <span class="home-promo-overlay"></span>
                <span class="home-promo-content">
                    <span class="home-badge">PROMO HARI INI</span>
                    <span class="home-promo-title">Diskon 50% untuk Vitamin &amp; Suplemen</span>
                    <span class="home-promo-subtitle">Spesial Hari Kelahiran Pancasila!</span>
                </span>
                <img class="home-promo-arrow" src="{{ asset('images/arrow-right.png') }}" alt="">
            </a>

            <section class="home-categories" aria-labelledby="home-categories-title">
                <div class="home-section-heading">
                    <h2 id="home-categories-title">Kategori Obat Populer</h2>
                    <a href="{{ route('toko') }}">Lihat Semua <span aria-hidden="true">›</span></a>
                </div>
                <div class="home-category-panel">
                    @foreach ($categories as $category)
                        <a class="home-category" href="{{ route('toko', ['kategori' => $category['query']]) }}">
                            <span class="home-category-icon">
                                <img src="{{ asset('images/'.$category['image']) }}" alt="" loading="lazy">
                            </span>
                            <span>{{ $category['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="home-articles" aria-labelledby="home-articles-title">
                <h2 id="home-articles-title">Artikel Kesehatan</h2>
                <a class="home-article-card" href="https://keslan.kemkes.go.id/view_artikel/2682/berbagai-manfaat-buah-buahan" target="_blank" rel="noopener noreferrer" aria-label="Manfaat Buah-Buahan Untuk Kesehatan" style="background-image: url('{{ asset('images/article-fruits.png') }}');">
                    <span class="home-article-overlay"></span>
                    <span class="home-article-content">
                        <span class="home-badge">TIPS NUTRISI</span>
                        <span class="home-article-title">Manfaat Buah-Buahan Untuk Kesehatan</span>
                    </span>
                    <img class="home-article-arrow" src="{{ asset('images/arrow-right.png') }}" alt="">
                </a>
            </section>
        </main>

        <nav class="beranda-bottom-nav" aria-label="Navigasi utama">
            <a class="nav-tab active" href="{{ route('beranda') }}" aria-current="page">
                <svg viewBox="0 0 32 32" aria-hidden="true">
                    <path d="m3 14 13-11 13 11v14H19v-9h-6v9H3z" />
                </svg>
                <span>Beranda</span>
            </a>
            <a class="nav-tab" href="{{ route('toko') }}">
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
</body>
</html>
