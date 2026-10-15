<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <title>Konsultasi | Wijaya Farma</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page consultation-page">
    @php
        $doctors = [
            ['name' => 'Dr. Jon D Ojan', 'experience' => '8 Tahun Pengalaman', 'rating' => '4.9', 'image' => 'dr-jon.png', 'image_class' => 'doctor-photo--jon', 'slug' => 'jon-d-ojan'],
            ['name' => 'Ir. Dr. Syarif', 'experience' => '40 Tahun Pengalaman', 'rating' => '5.0', 'image' => 'dr-syarif.png', 'image_class' => 'doctor-photo--syarif', 'slug' => 'syarif'],
            ['name' => 'Dr. Fatir D H', 'experience' => '100 Tahun Pengalaman', 'rating' => '4.9', 'image' => 'dr-fatir.png', 'image_class' => 'doctor-photo--fatir', 'slug' => 'fatir-d-h'],
        ];
    @endphp

    <div class="beranda-screen consultation-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="consultation-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                    <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
                </span>
            </div>
            <h1>Konsultasi</h1>
        </header>

        <main class="consultation-content" aria-labelledby="available-doctors">
            <h2 id="available-doctors">Dokter Umum Tersedia</h2>
            <div class="doctor-list">
                @foreach ($doctors as $doctor)
                    <article class="doctor-card">
                        <img class="doctor-photo {{ $doctor['image_class'] }}" src="{{ asset('images/konsultasi/'.$doctor['image']) }}" alt="Foto {{ $doctor['name'] }}">
                        <div class="doctor-rating" aria-label="Rating {{ $doctor['rating'] }} dari 5">
                            <img src="{{ asset('images/konsultasi/star.png') }}" alt="">
                            <span>{{ $doctor['rating'] }}</span>
                        </div>
                        <h3>{{ $doctor['name'] }}</h3>
                        <p class="doctor-specialty">Dokter Umum</p>
                        <div class="doctor-experience">
                            <img src="{{ asset('images/konsultasi/work-time.png') }}" alt="">
                            <span>{{ $doctor['experience'] }}</span>
                        </div>
                        <a class="doctor-chat" href="{{ route('konsultasi.chat', $doctor['slug']) }}">
                            <img src="{{ asset('images/konsultasi/chat.png') }}" alt="">
                            <span>Chat Sekarang</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </main>

        <nav class="beranda-bottom-nav" aria-label="Navigasi utama">
            <a class="nav-tab" href="{{ route('beranda') }}">
                <svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 14 13-11 13 11v14H19v-9h-6v9H3z" /></svg>
                <span>Beranda</span>
            </a>
            <a class="nav-tab" href="{{ route('toko') }}">
                <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9" /><circle cx="15" cy="33" r="2.8" /><circle cx="31" cy="33" r="2.8" /></svg>
                <span>Toko</span>
            </a>
            <a class="nav-tab active" href="{{ route('konsultasi') }}" aria-current="page">
                <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z" /></svg>
                <span>Konsultasi</span>
            </a>
            <a class="nav-tab" href="{{ route('riwayat') }}">
                <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M18 4C9.7 4 3 6.8 3 10.2v21.3c4.3-2.6 9.4-3.9 15-3.9s10.7 1.3 15 3.9V10.2C33 6.8 26.3 4 18 4Z" /><path d="M18 5v23" /></svg>
                <span>Riwayat</span>
            </a>
            <a class="nav-tab" href="{{ route('profil') }}">
                <svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6" /><path d="M5 32a13 13 0 0 1 26 0z" /></svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>
</body>
</html>
