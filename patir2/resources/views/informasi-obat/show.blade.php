<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <title>Informasi {{ $medicine['name'] }} | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page toko-page">
    <div class="beranda-screen medicine-info-screen">
        <header class="beranda-header">
            <div class="status-bar" aria-label="Waktu 9:41"><span>9:41</span><span class="status-icons" aria-hidden="true">
                <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
            </span></div>
            <h1>Informasi</h1>
            <button class="category-toggle" type="button" aria-label="Kategori obat">
                <svg class="category-list-icon" viewBox="0 0 28 28" aria-hidden="true"><circle cx="4" cy="6" r="2"/><circle cx="4" cy="14" r="2"/><circle cx="4" cy="22" r="2"/><path d="M10 6h15M10 14h15M10 22h15"/></svg>
            </button>
            <div class="store-search" role="search">
                <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="7.2"/><path d="m16.2 16.2 5 5"/></svg>
                <span>Cari obat atau kategori...</span>
            </div>
        </header>

        <main class="product-detail-screen">
            <article class="detail-card">
                <div class="detail-product-image"><img src="{{ asset('images/'.$medicine['image']) }}" alt="{{ $medicine['name'] }}"></div>
                <div class="detail-copy">
                    <span class="product-category">{{ $medicine['category'] }}</span>
                    <h2>{{ $medicine['display_name'] }}</h2>
                    <p class="detail-line"><strong>Nama Obat</strong><span> : {{ $medicine['name'] }}</span></p>
                    <p class="detail-line"><strong>Kandungan</strong><span> : {{ $medicine['content'] }}</span></p>
                    <div class="detail-dose"><strong>Dosis :</strong><ul>
                        @foreach ($medicine['dose'] as $dose)
                            <li>{!! $dose !!}</li>
                        @endforeach
                    </ul></div>
                    <p class="detail-description">{{ $medicine['description'] }}</p>
                    <p class="detail-warning">Peringatan : {{ $medicine['warning'] }}</p>
                </div>
            </article>
            <a class="detail-back" href="{{ route('toko') }}" aria-label="Kembali ke toko"><span aria-hidden="true">‹</span></a>
        </main>

        <nav class="beranda-bottom-nav" aria-label="Navigasi utama">
            <a class="nav-tab" href="{{ route('beranda') }}"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 14 13-11 13 11v14H19v-9h-6v9H3z"/></svg><span>Beranda</span></a>
            <a class="nav-tab active" href="{{ route('checkout') }}"><svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9"/><circle cx="15" cy="33" r="2.8"/><circle cx="31" cy="33" r="2.8"/></svg><span>Toko</span></a>
            <a class="nav-tab" href="{{ route('konsultasi') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z"/></svg><span>Konsultasi</span></a>
            <a class="nav-tab" href="{{ route('riwayat') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><path d="M3 6c6-2 11-1 15 2 4-3 9-4 15-2v24c-6-2-11-1-15 2-4-3-9-4-15-2zM18 8v24"/></svg><span>Riwayat</span></a>
            <a class="nav-tab" href="{{ route('profil') }}"><svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6"/><path d="M5 32a13 13 0 0 1 26 0z"/></svg><span>Profil</span></a>
        </nav>
    </div>
</body>
</html>
