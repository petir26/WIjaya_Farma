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
<body class="beranda-page customer-service-page">
    <div class="beranda-screen customer-service-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="customer-service-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                    <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
                </span>
            </div>
            <a class="customer-service-back" href="{{ route('profil') }}" aria-label="Kembali ke profil">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5m7 7-7-7 7-7" /></svg>
            </a>
            <h1>Customer Service</h1>
        </header>

        <main class="customer-service-content">
            <section class="customer-service-hero" aria-labelledby="cs-greeting">
                <h2 id="cs-greeting">Halo! Ada yang bisa kami bantu?</h2>
                <p>Tanyakan apa saja kepada Admin kami untuk jawaban mengenai masalah layanan kesehatan Anda.</p>
                <button class="customer-service-chat-button" type="button" id="open-admin-chat">
                    <img src="{{ asset('images/customer-service/support.png') }}" alt="">
                    <span>Mulai Chat Dengan Admin</span>
                </button>
            </section>

            <section class="customer-service-quick" aria-labelledby="quick-help-title">
                <h2 id="quick-help-title">Bantuan Cepat</h2>
                <div class="quick-help-grid">
                    <a class="quick-help-card" href="{{ route('riwayat') }}">
                        <img src="{{ asset('images/customer-service/express-delivery.png') }}" alt="">
                        <strong>Status Pesanan</strong>
                        <span>Lacak Pengiriman</span>
                    </a>
                    <a class="quick-help-card" href="{{ route('riwayat') }}">
                        <img src="{{ asset('images/customer-service/cashback.png') }}" alt="">
                        <strong>Pengembalian</strong>
                        <span>Kebijakan Retur</span>
                    </a>
                    <a class="quick-help-card" href="{{ route('checkout') }}">
                        <img src="{{ asset('images/customer-service/money.png') }}" alt="">
                        <strong>Pembayaran</strong>
                        <span>Metode &amp; Tagihan</span>
                    </a>
                    <a class="quick-help-card" href="{{ route('profil') }}">
                        <img src="{{ asset('images/customer-service/user.png') }}" alt="">
                        <strong>Tentang Akun</strong>
                        <span>Data Kesehatan</span>
                    </a>
                </div>
            </section>

            <section class="customer-service-faq" aria-labelledby="faq-title">
                <h2 id="faq-title">Mungkin Membantu Anda</h2>
                <div class="faq-list">
                    <details class="faq-item">
                        <summary><img class="faq-question-icon" src="{{ asset('images/customer-service/faq-circle.svg') }}" alt=""><span>Bagaimana cara memesan obat resep?</span><img class="faq-chevron" src="{{ asset('images/customer-service/chevron.svg') }}" alt=""></summary>
                        <p>Pilih obat yang diperlukan di menu Toko, lalu lanjutkan ke checkout untuk meninjau dan mengonfirmasi pesanan.</p>
                    </details>
                    <details class="faq-item">
                        <summary><img class="faq-question-icon" src="{{ asset('images/customer-service/faq-circle.svg') }}" alt=""><span>Bagaimana cara memesan obat resep?</span><img class="faq-chevron" src="{{ asset('images/customer-service/chevron.svg') }}" alt=""></summary>
                        <p>Untuk obat resep, siapkan resep dokter Anda dan hubungi Admin agar pesanan dapat dibantu.</p>
                    </details>
                    <details class="faq-item">
                        <summary><img class="faq-question-icon" src="{{ asset('images/customer-service/faq-circle.svg') }}" alt=""><span>Bagaimana cara memesan obat resep?</span><img class="faq-chevron" src="{{ asset('images/customer-service/chevron.svg') }}" alt=""></summary>
                        <p>Pastikan alamat pengiriman dan nomor telepon Anda sudah benar sebelum mengonfirmasi checkout.</p>
                    </details>
                </div>
            </section>
        </main>
    </div>

    <dialog class="customer-service-dialog" id="admin-chat-dialog" aria-labelledby="admin-chat-title">
        <h2 id="admin-chat-title">Chat Admin</h2>
        <p>Pesan Anda tersimpan. Balasan akan ditampilkan setelah fitur Admin tersedia.</p>
        <div id="customer-service-messages" class="customer-service-messages" aria-live="polite"></div>
        <form id="customer-service-message-form">
            <label class="visually-hidden" for="customer-service-message">Pesan untuk Admin</label>
            <textarea id="customer-service-message" name="message" rows="3" maxlength="5000" placeholder="Tulis pesan..." required></textarea>
            <button type="submit" id="send-customer-service-message">Kirim</button>
            <button type="button" id="close-admin-chat">Tutup</button>
        </form>
    </dialog>
    <script>
        const adminChatDialog = document.querySelector('#admin-chat-dialog');
        const customerServiceMessages = document.querySelector('#customer-service-messages');
        const customerServiceForm = document.querySelector('#customer-service-message-form');
        const customerServiceInput = document.querySelector('#customer-service-message');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const renderCustomerServiceMessages = async () => {
            const response = await fetch('{{ route('client.customer-service.index') }}', { headers: { 'Accept': 'application/json' } });
            if (!response.ok) return;
            const result = await response.json();
            customerServiceMessages.replaceChildren();
            result.messages.data.forEach((item) => {
                const message = document.createElement('p');
                message.className = 'customer-service-message customer-service-message--client';
                message.textContent = item.message;
                customerServiceMessages.append(message);
                if (item.admin_reply) {
                    const reply = document.createElement('p');
                    reply.className = 'customer-service-message customer-service-message--admin';
                    reply.textContent = item.admin_reply;
                    customerServiceMessages.append(reply);
                }
            });
        };
        document.querySelector('#open-admin-chat').addEventListener('click', () => {
            adminChatDialog.showModal();
            renderCustomerServiceMessages();
        });
        document.querySelector('#close-admin-chat').addEventListener('click', () => adminChatDialog.close());
        customerServiceForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const sendButton = document.querySelector('#send-customer-service-message');
            sendButton.disabled = true;
            try {
                const response = await fetch('{{ route('client.customer-service.store') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ message: customerServiceInput.value.trim() }),
                });
                const result = await response.json();
                if (!response.ok) { window.alert(result.message || 'Pesan gagal dikirim.'); return; }
                customerServiceInput.value = '';
                await renderCustomerServiceMessages();
            } catch (error) {
                window.alert('Gagal menghubungi server. Coba lagi.');
            } finally {
                sendButton.disabled = false;
            }
        });
    </script>
</body>
</html>
