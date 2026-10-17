<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Service | Wijaya Farma</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page doctor-chat-page cs-chat-page">
    <div class="beranda-screen doctor-chat-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="doctor-chat-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                    <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
                </span>
            </div>
            <div class="doctor-chat-profile">
                <a class="chat-back" href="{{ route('customer-service') }}" aria-label="Kembali ke Customer Service">
                    <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M19 5 8 16l11 11M9 16h20" /></svg>
                </a>
                <img class="chat-doctor-avatar" src="{{ asset('images/support.png') }}" alt="Customer Service Avatar" style="object-fit: contain; background: rgba(255, 255, 255, 0.15); padding: 4px;">
                <div class="chat-doctor-copy">
                    <h1>Customer Service</h1>
                    <p>Admin - Online</p>
                </div>
            </div>
        </header>

        <main class="doctor-chat-stream" id="chat-stream" aria-label="Percakapan dengan Customer Service" aria-live="polite"></main>

        <form class="doctor-chat-composer" id="chat-form">
            <div class="chat-input-wrap">
                <label class="visually-hidden" for="chat-message">Ketik pesan</label>
                <input id="chat-message" type="text" placeholder="Ketik pesan..." autocomplete="off">
                <input class="visually-hidden" id="chat-attachment" type="file" aria-label="Pilih lampiran">
                <input class="visually-hidden" id="chat-camera" type="file" accept="image/*" capture="environment" aria-label="Ambil foto">
                <button class="chat-attachment-button" type="button" data-file-input="chat-attachment" aria-label="Lampirkan file">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8.5 12.5 7.1-7.1a3.5 3.5 0 0 1 4.9 5l-9.4 9.4a5 5 0 0 1-7.1-7.1l9-9" /></svg>
                </button>
                <button class="chat-camera-button" type="button" data-file-input="chat-camera" aria-label="Ambil foto">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h4l1.6-2h6.8L17 7h4v12H3z" /><circle cx="12" cy="13" r="3.5" /></svg>
                </button>
            </div>
            <button class="chat-send-button" type="submit" aria-label="Kirim pesan">
                <svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 14 26-11-11 26-4-11zM14 18 29 3" /></svg>
            </button>
        </form>

        <nav class="beranda-bottom-nav" aria-label="Navigasi utama">
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
                <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M18 4C9.7 4 3 6.8 3 10.2v21.3c4.3-2.6 9.4-3.9 15-3.9s10.7 1.3 15 3.9V10.2C33 6.8 26.3 4 18 4Z" /><path d="M18 5v23" /></svg>
                <span>Riwayat</span>
            </a>
            <a class="nav-tab active" href="{{ route('profil') }}" aria-current="page">
                <svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6" /><path d="M5 32a13 13 0 0 1 26 0z" /></svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <script>
        const chatForm = document.querySelector('#chat-form');
        const chatMessage = document.querySelector('#chat-message');
        const chatStream = document.querySelector('#chat-stream');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let consultationId = new URLSearchParams(window.location.search).get('consultation');
        let chatMessages = [];

        const renderMessages = (messages) => {
            chatMessages = messages || [];
            chatStream.replaceChildren();
            chatMessages.forEach((item) => {
                const bubble = document.createElement('p');
                bubble.className = item.sender_type === 'doctor' ? 'chat-incoming-message' : 'chat-outgoing-message';
                bubble.textContent = item.message;
                chatStream.append(bubble);
            });
            chatStream.scrollTop = chatStream.scrollHeight;
        };

        if (consultationId) {
            fetch(`{{ url('/client/consultations') }}/${consultationId}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then((res) => res.ok ? res.json() : Promise.reject())
            .then((result) => renderMessages(result.consultation.messages))
            .catch(() => { consultationId = null; });
        }

        chatForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = chatMessage.value.trim();
            if (!message) return;

            const sendButton = chatForm.querySelector('[type="submit"]');
            sendButton.disabled = true;

            try {
                const endpoint = consultationId 
                    ? `{{ url('/client/consultations') }}/${consultationId}/messages` 
                    : '{{ route('client.consultations.store') }}';
                const body = consultationId 
                    ? { message } 
                    : { doctor_name: 'Customer Service', complaint: message };

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(body)
                });

                const result = await response.json();

                if (!response.ok) {
                    window.alert(result.message || 'Pesan gagal dikirim.');
                    return;
                }

                if (!consultationId) {
                    // Sesi konsultasi CS baru berhasil dibuat
                    consultationId = result.consultation.id;
                    history.replaceState({}, '', `?consultation=${consultationId}`);
                    renderMessages(result.consultation.messages);
                } else {
                    renderMessages([...chatMessages, result.item]);
                }

                chatMessage.value = '';
                chatMessage.focus();
            } catch (error) {
                window.alert('Gagal menghubungi server. Silahkan periksa koneksi.');
            } finally {
                sendButton.disabled = false;
            }
        });

        document.querySelectorAll('[data-file-input]').forEach((button) => {
            button.addEventListener('click', () => document.getElementById(button.dataset.fileInput).click());
        });
    </script>
</body>
</html>
