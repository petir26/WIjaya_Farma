<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat {{ $consultation->user?->name ?? 'Klien' }} | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page doctor-chat-page">
    <div class="beranda-screen doctor-chat-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="doctor-chat-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true"><svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg><svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg><svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg></span>
            </div>
            <div class="doctor-chat-profile">
                <a class="chat-back" href="{{ route('doctor.consultations.index') }}" aria-label="Kembali ke konsultasi"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M19 5 8 16l11 11M9 16h20" /></svg></a>
                <img class="chat-doctor-avatar" src="{{ asset('images/konsultasi/pak-rahman.jpeg') }}" alt="">
                <div class="chat-doctor-copy"><h1>{{ $consultation->user?->name ?? 'Klien' }}</h1><p>{{ $consultation->user?->email ?? 'Klien Wijaya Farma' }}</p></div>
            </div>
        </header>

        <main class="doctor-chat-stream" id="chat-stream" aria-label="Percakapan dengan {{ $consultation->user?->name ?? 'klien' }}" aria-live="polite">
            @foreach ($consultation->messages as $message)
                <div class="{{ $message->sender_type === 'doctor' ? 'chat-incoming-message doctor-reply-message' : 'chat-outgoing-message client-question-message' }}">
                    @if ($message->message)<span>{{ $message->message }}</span>@endif
                    @if ($message->attachment_path)
                        <img src="{{ route('doctor.consultations.attachment', $message) }}" alt="Lampiran dari klien" loading="lazy">
                    @endif
                </div>
            @endforeach
        </main>

        <form class="doctor-chat-composer" id="doctor-chat-form" action="{{ route('doctor.consultations.reply', $consultation) }}">
            <label class="visually-hidden" for="doctor-chat-message">Ketik balasan untuk klien</label>
            <div class="chat-input-wrap">
                <input id="doctor-chat-message" type="text" placeholder="Ketik pesan..." autocomplete="off" maxlength="5000">
                <input class="visually-hidden" id="doctor-chat-attachment" type="file" accept="image/*" aria-label="Pilih gambar untuk dikirim">
                <input class="visually-hidden" id="doctor-chat-camera" type="file" accept="image/*" capture="environment" aria-label="Ambil foto untuk dikirim">
                <span class="chat-attachment-status" id="doctor-attachment-status" aria-live="polite"></span>
                <button class="chat-attachment-button" type="button" data-file-input="doctor-chat-attachment" aria-label="Lampirkan foto"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8.5 12.5 7.1-7.1a3.5 3.5 0 0 1 4.9 5l-9.4 9.4a5 5 0 0 1-7.1-7.1l9-9" /></svg></button>
                <button class="chat-camera-button" type="button" data-file-input="doctor-chat-camera" aria-label="Ambil foto"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h4l1.6-2h6.8L17 7h4v12H3z" /><circle cx="12" cy="13" r="3.5" /></svg></button>
            </div>
            <button class="chat-send-button" type="submit" aria-label="Kirim balasan"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 14 26-11-11 26-4-11zM14 18 29 3" /></svg></button>
        </form>

        <nav class="doctor-portal-nav" aria-label="Navigasi dokter">
            <a class="doctor-portal-tab doctor-portal-tab--active" href="{{ route('doctor.consultations.index') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z" /></svg><span>Konsultasi</span></a>
            <a class="doctor-portal-tab" href="{{ route('doctor.profile') }}"><svg viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6"/><path d="M5 32a13 13 0 0 1 26 0z"/></svg><span>Profil</span></a>
        </nav>
    </div>
    <script>
        const form = document.querySelector('#doctor-chat-form');
        const input = document.querySelector('#doctor-chat-message');
        const stream = document.querySelector('#chat-stream');
        const attachmentInputs = [document.querySelector('#doctor-chat-attachment'), document.querySelector('#doctor-chat-camera')];
        const attachmentStatus = document.querySelector('#doctor-attachment-status');
        const token = document.querySelector('meta[name="csrf-token"]').content;
        stream.scrollTop = stream.scrollHeight;
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = input.value.trim();
            const attachment = attachmentInputs.find((fileInput) => fileInput.files?.[0])?.files[0];
            if (!message && !attachment) return;
            const button = form.querySelector('button');
            button.disabled = true;
            try {
                const body = new FormData();
                if (message) body.append('message', message);
                if (attachment) body.append('attachment', attachment);
                const response = await fetch(form.action, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Balasan gagal dikirim.');
                const bubble = document.createElement('div');
                bubble.className = 'chat-incoming-message doctor-reply-message';
                if (result.item.message) {
                    const text = document.createElement('span');
                    text.textContent = result.item.message;
                    bubble.append(text);
                }
                if (result.item.attachment_url) {
                    const image = document.createElement('img');
                    image.src = result.item.attachment_url;
                    image.alt = 'Foto yang dikirim dokter';
                    bubble.append(image);
                }
                stream.append(bubble);
                stream.scrollTop = stream.scrollHeight;
                input.value = '';
                attachmentInputs.forEach((fileInput) => { fileInput.value = ''; });
                attachmentStatus.textContent = '';
                input.focus();
            } catch (error) {
                window.alert(error.message || 'Gagal menghubungi server. Coba lagi.');
            } finally {
                button.disabled = false;
            }
        });
        document.querySelectorAll('[data-file-input]').forEach((button) => button.addEventListener('click', () => document.getElementById(button.dataset.fileInput).click()));
        attachmentInputs.forEach((fileInput) => fileInput.addEventListener('change', () => {
            if (fileInput.files?.[0]) attachmentInputs.filter((other) => other !== fileInput).forEach((other) => { other.value = ''; });
            const selected = attachmentInputs.find((other) => other.files?.[0])?.files[0];
            attachmentStatus.textContent = selected ? `Foto terlampir: ${selected.name}` : '';
        }));
    </script>
</body>
</html>
