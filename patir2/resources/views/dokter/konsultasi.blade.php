<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <title>Konsultasi Pasien | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page doctor-chat-page">
    <div class="beranda-screen doctor-chat-screen doctor-inbox-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="doctor-chat-header doctor-inbox-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true"><svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg><svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg><svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg></span>
            </div>
            <h1 class="doctor-inbox-title">Inbox</h1>
        </header>
        <main class="doctor-inbox-list" aria-label="Daftar konsultasi">
            @forelse ($consultations as $consultation)
                <a class="doctor-inbox-card" href="{{ route('doctor.consultations.show', $consultation) }}">
                    @php
                        $latestMessage = $consultation->messages->first();
                        $needsReply = $latestMessage?->sender_type === 'client';
                    @endphp
                    <img src="{{ asset('images/konsultasi/dr-syarif.png') }}" alt="" class="doctor-inbox-avatar">
                    <span class="doctor-inbox-copy">
                        <strong>{{ $consultation->user?->name ?? 'Klien' }}</strong>
                        <span class="doctor-inbox-specialty">Client</span>
                        <span class="doctor-inbox-presence {{ $needsReply ? 'doctor-inbox-presence--online' : '' }}"><i></i>{{ $needsReply ? 'online' : 'Dikirim' }}</span>
                    </span>
                    <span class="doctor-inbox-time">{{ $consultation->updated_at->format('H.i') }} WITA</span>
                    <span class="doctor-inbox-preview">{{ $latestMessage?->message ?? $consultation->complaint }}</span>
                    @if ($needsReply)<span class="doctor-inbox-unread" aria-label="Ada pesan baru">1</span>@endif
                </a>
            @empty
                <div class="doctor-inbox-empty"><span class="doctor-empty-icon" aria-hidden="true">♡</span><h2>Belum ada pesan</h2><p>Konsultasi dari klien akan tampil di sini.</p></div>
            @endforelse
        </main>
        <nav class="doctor-portal-nav" aria-label="Navigasi dokter">
            <a class="doctor-portal-tab doctor-portal-tab--active" href="{{ route('doctor.consultations.index') }}" aria-current="page"><svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z" /></svg><span>Konsultasi</span></a>
            <a class="doctor-portal-tab" href="{{ route('doctor.profile') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6"/><path d="M5 32a13 13 0 0 1 26 0z"/></svg><span>Profil</span></a>
        </nav>
    </div>
</body>
</html>
