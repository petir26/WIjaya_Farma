<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil | Wijaya Farma</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page profile-page">
    <div class="beranda-screen profile-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="profile-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                    <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
                </span>
            </div>
            <h1>Profil</h1>
            @if (auth()->user()->role !== 'admin')
            <a class="profile-support-link" href="{{ route('customer-service') }}" aria-label="Buka Customer Service">
                <img class="profile-support-icon" src="{{ asset('images/support.png') }}" alt="">
            </a>
            @endif
        </header>

        <main class="profile-content">
            <form class="profile-form" id="profile-form" autocomplete="on">
                <div class="profile-avatar" aria-label="Foto profil">
                    <svg viewBox="0 0 84 84" aria-hidden="true">
                        <circle cx="42" cy="42" r="32" />
                        <circle cx="42" cy="34" r="8" />
                        <path d="M27 62c2-10 7-15 15-15s13 5 15 15" />
                    </svg>
                </div>

                <div class="profile-field profile-field--username">
                    <label for="profile-name">Nama Pengguna :</label>
                    <input id="profile-name" name="name" type="text" autocomplete="name" value="{{ old('name', $client->name) }}">
                </div>

                <div class="profile-field profile-field--email">
                    <label for="profile-email">Email :</label>
                    <input id="profile-email" name="email" type="email" autocomplete="email" value="{{ old('email', $client->email) }}">
                </div>

                <div class="profile-field profile-field--phone">
                    <label for="profile-phone">Nomor Telepon :</label>
                    <input id="profile-phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', $client->phone) }}">
                </div>

                <div class="profile-field profile-field--blood">
                    <label for="profile-blood">Golongan Darah :</label>
                    <select id="profile-blood" name="blood_type" aria-label="Golongan darah">
                        <option value=""></option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bloodType)
                            <option value="{{ $bloodType }}" @selected(old('blood_type', $client->blood_type) === $bloodType)>{{ $bloodType }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="profile-measurements">
                    <div class="profile-field profile-field--weight">
                        <label for="profile-weight">Berat Badan :</label>
                        <div class="profile-input-with-unit">
                            <input id="profile-weight" name="weight_kg" type="number" min="0" step="0.1" inputmode="decimal" value="{{ old('weight_kg', $client->weight_kg) }}">
                            <span>Kg</span>
                        </div>
                    </div>
                    <div class="profile-field profile-field--height">
                        <label for="profile-height">Tinggi Badan :</label>
                        <div class="profile-input-with-unit">
                            <input id="profile-height" name="height_cm" type="number" min="0" step="0.1" inputmode="decimal" value="{{ old('height_cm', $client->height_cm) }}">
                            <span>Cm</span>
                        </div>
                    </div>
                </div>

                <div class="profile-field profile-field--medical-history">
                    <label for="profile-medical-history">Riwayat Penyakit :</label>
                    <textarea id="profile-medical-history" name="medical_history" rows="3">{{ old('medical_history', $client->medical_history) }}</textarea>
                </div>

                <div class="profile-field profile-field--address">
                    <label for="profile-address">Alamat :</label>
                    <textarea id="profile-address" name="address" rows="4" autocomplete="street-address">{{ old('address', $client->address) }}</textarea>
                </div>

                <button class="profile-logout" type="submit" form="logout-form" aria-label="Keluar">
                    <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M14 6H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h7M18 10l7 6-7 6M11 16h14" /></svg>
                </button>
                <p class="profile-save-status" id="profile-save-status" role="status" aria-live="polite" hidden>Perubahan profil tersimpan.</p>
            </form>
        </main>

        <nav class="beranda-bottom-nav" aria-label="{{ auth()->user()->role === 'admin' ? 'Navigasi admin' : 'Navigasi utama' }}">
            @if (auth()->user()->role === 'admin')
            <a class="nav-tab" href="{{ route('admin.toko') }}">
                <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9" /><circle cx="15" cy="33" r="2.8" /><circle cx="31" cy="33" r="2.8" /></svg>
                <span>Toko</span>
            </a>
            @else
            <a class="nav-tab" href="{{ route('beranda') }}">
                <svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 14 13-11 13 11v14H19v-9h-6v9H3z" /></svg>
                <span>Beranda</span>
            </a>
            <a class="nav-tab" href="{{ route('toko') }}">
                <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M3 5h4l3.5 18.5a4 4 0 0 0 4 3.2h17a4 4 0 0 0 3.9-3l3.1-12.3H9" /><circle cx="15" cy="33" r="2.8" /><circle cx="31" cy="33" r="2.8" /></svg>
                <span>Toko</span>
            </a>
            <a class="nav-tab" href="{{ route('konsultasi') }}">
                <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z" /></svg>
                <span>Konsultasi</span>
            </a>
            <a class="nav-tab" href="{{ route('riwayat') }}">
                <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M3 6c6-2 11-1 15 2 4-3 9-4 15-2v24c-6-2-11-1-15 2-4-3-9-4-15-2zM18 8v24" /></svg>
                <span>Riwayat</span>
            </a>
            @endif
            <a class="nav-tab active" href="{{ route('profil') }}" aria-current="page">
                <svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6" /><path d="M5 32a13 13 0 0 1 26 0z" /></svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="post" hidden>@csrf</form>

    <script>
        const profileForm = document.querySelector('#profile-form');
        const profileSaveStatus = document.querySelector('#profile-save-status');
        let profileSaveTimer;
        const queueProfileSave = () => {
            clearTimeout(profileSaveTimer);
            profileSaveTimer = setTimeout(async () => {
                const values = Object.fromEntries(new FormData(profileForm).entries());
                try {
                    const response = await fetch('{{ route('profil.update') }}', {
                        method: 'PATCH',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(values),
                    });
                    const result = await response.json();
                    profileSaveStatus.textContent = response.ok ? result.message : (result.message || Object.values(result.errors || {}).flat()[0] || 'Profil belum berhasil disimpan.');
                } catch (error) {
                    profileSaveStatus.textContent = 'Profil belum berhasil disimpan. Periksa koneksi Anda.';
                }
                profileSaveStatus.hidden = false;
            }, 500);
        };
        profileForm.addEventListener('input', queueProfileSave);
        profileForm.addEventListener('change', queueProfileSave);
    </script>
</body>
</html>
