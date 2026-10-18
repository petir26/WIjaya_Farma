<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Dokter | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page profile-page">
    <div class="beranda-screen profile-screen doctor-profile-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="profile-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true"><svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg><svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg><svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg></span>
            </div>
            <h1>Profil</h1>
        </header>

        <main class="profile-content doctor-profile-content">
            <form class="doctor-profile-form" id="doctor-profile-form" autocomplete="on">
                <div class="profile-avatar" aria-hidden="true"><svg viewBox="0 0 84 84"><circle cx="42" cy="42" r="32"/><circle cx="42" cy="34" r="8"/><path d="M27 62c2-10 7-15 15-15s13 5 15 15"/></svg></div>
                <div class="profile-field"><label for="doctor-profile-name">Nama Dokter</label><input id="doctor-profile-name" name="name" type="text" autocomplete="name" value="{{ old('name', $doctor->name) }}" required></div>
                <div class="profile-field"><label for="doctor-profile-email">Email</label><input id="doctor-profile-email" name="email" type="email" autocomplete="email" value="{{ old('email', $doctor->email) }}" required></div>
                <div class="profile-field"><label for="doctor-profile-phone">Nomor Telepon</label><input id="doctor-profile-phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', $doctor->phone) }}"></div>
                <button class="profile-logout" type="submit" form="doctor-logout-form" aria-label="Keluar"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M14 6H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h7M18 10l7 6-7 6M11 16h14" /></svg></button>
                <p class="profile-save-status" id="doctor-profile-status" role="status" aria-live="polite" hidden></p>
            </form>
        </main>

        <nav class="doctor-portal-nav" aria-label="Navigasi dokter">
            <a class="doctor-portal-tab" href="{{ route('doctor.consultations.index') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z" /></svg><span>Konsultasi</span></a>
            <a class="doctor-portal-tab doctor-portal-tab--active" href="{{ route('doctor.profile') }}" aria-current="page"><svg viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6"/><path d="M5 32a13 13 0 0 1 26 0z"/></svg><span>Profil</span></a>
        </nav>
    </div>
    <form id="doctor-logout-form" action="{{ route('logout') }}" method="post" hidden>@csrf</form>
    <script>
        const profileForm = document.querySelector('#doctor-profile-form');
        const status = document.querySelector('#doctor-profile-status');
        let saveTimer;
        const saveProfile = () => {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(async () => {
                try {
                    const response = await fetch('{{ route('doctor.profile.update') }}', {
                        method: 'PATCH',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(Object.fromEntries(new FormData(profileForm).entries())),
                    });
                    const result = await response.json();
                    status.textContent = response.ok ? result.message : (result.message || 'Profil belum berhasil disimpan.');
                } catch (error) {
                    status.textContent = 'Profil belum berhasil disimpan. Periksa koneksi Anda.';
                }
                status.hidden = false;
            }, 500);
        };
        profileForm.addEventListener('input', saveProfile);
        profileForm.addEventListener('change', saveProfile);
    </script>
</body>
</html>
