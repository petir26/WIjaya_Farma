<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <title>Ubah Alamat | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="address-picker-page">
    <main class="address-picker-screen">
        <div class="address-map" aria-label="Peta lokasi apotek">
            <img class="address-map-image" src="{{ asset('images/pharmacy-map.png') }}" alt="Peta Banjarmasin dengan lokasi cabang Wijaya Farma">
            <img class="address-map-pin" src="{{ asset('images/map-pin.png') }}" alt="Lokasi apotek terpilih">
        </div>
        <header class="address-picker-header">
            <div class="confirm-status"><strong>9:41</strong><span aria-hidden="true"><i></i><i></i><i></i><i></i><b>◔</b><em></em></span></div>
            <a href="{{ route('checkout.confirmation') }}" class="address-picker-back" aria-label="Kembali ke konfirmasi">‹</a>
            <h1>Ubah Alamat</h1>
        </header>
        <section class="pharmacy-sheet" aria-label="Pilih cabang apotek">
            <div class="sheet-handle" aria-hidden="true"></div>
            <div class="pharmacy-list">
                <article class="pharmacy-choice" data-branch-card>
                    <h2>Wijaya Farma, Cabang Banjarmasin Tengah</h2>
                    <p class="branch-rating"><img src="{{ asset('images/map-star.svg') }}" alt="">3,8 (2.502)</p>
                    <p class="branch-distance">1,6 Km dari anda</p>
                    <button type="button" data-select-branch data-name="Banjarmasin Tengah" data-address="Jl. Gatot Subroto No. 45, Banjarmasin Tengah">Pilih</button>
                </article>
                <article class="pharmacy-choice pharmacy-choice--second" role="button" tabindex="0" aria-label="Pilih Wijaya Farma Banjarmasin Selatan" data-branch-select data-name="Banjarmasin Selatan" data-address="Jl. Basuki Rahmat, Banjarmasin Selatan">
                    <h2>Wijaya Farma, Cabang Banjarmasin Selatan</h2>
                    <p class="branch-rating"><img src="{{ asset('images/map-star.svg') }}" alt="">4,8 (3.538)</p>
                    <p class="branch-distance">3,6 Km dari anda</p>
                </article>
            </div>
        </section>
    </main>
    <script>
        const selectBranch = (button) => {
            localStorage.setItem('wijaya-shipping-address', button.dataset.address);
            localStorage.setItem('wijaya-pharmacy-branch', button.dataset.name);
            window.location.href = '{{ route('checkout.confirmation') }}';
        };
        document.querySelectorAll('[data-select-branch]').forEach((button) => {
            button.addEventListener('click', () => {
                selectBranch(button);
            });
        });
        const secondBranch = document.querySelector('[data-branch-select]');
        secondBranch.addEventListener('click', () => selectBranch(secondBranch));
        secondBranch.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); selectBranch(secondBranch); }
        });
    </script>
</body>
</html>
