<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat | Wijaya Farma</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="beranda-page history-page">
    <div class="beranda-screen history-screen" style="--consultation-pattern: url('{{ asset('images/konsultasi/background.png') }}')">
        <header class="history-header">
            <div class="status-bar" aria-label="Waktu 9:41">
                <span>9:41</span>
                <span class="status-icons" aria-hidden="true">
                    <svg class="signal-icon" viewBox="0 0 24 16"><path d="M2 12h3v3H2zm5-4h3v7H7zm5-4h3v11h-3zm5-3h3v14h-3z" /></svg>
                    <svg class="wifi-icon" viewBox="0 0 20 16"><path d="M1 5a13 13 0 0 1 18 0M4.5 8.5a8 8 0 0 1 11 0M8 12a3 3 0 0 1 4 0" /><circle cx="10" cy="14" r=".8" /></svg>
                    <svg class="battery-icon" viewBox="0 0 28 16"><rect x="1" y="1.5" width="23" height="13" rx="3.5" /><path d="M26 5.5v5" /></svg>
                </span>
            </div>
            <h1>Riwayat</h1>
        </header>

        <main class="history-content">
            <div class="history-tabs-wrapper">
                <div class="history-tabs" role="tablist" aria-label="Jenis riwayat">
                    <button class="history-tab is-selected" id="purchases-tab" type="button" role="tab" aria-selected="true" aria-controls="purchases-panel">Pembelian</button>
                    <button class="history-tab" id="consultations-tab" type="button" role="tab" aria-selected="false" aria-controls="consultations-panel">Konsultasi</button>
                </div>
            </div>

            <section class="history-panel" id="purchases-panel" role="tabpanel" aria-labelledby="purchases-tab">
                @forelse ($orders as $order)
                    <article class="purchase-history-card">
                        <h2>Order #{{ $order->order_number }}</h2>
                        <div class="purchase-status"><span>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span><span class="purchase-status-check" aria-hidden="true">✓</span></div>
                        @foreach ($order->items as $item)
                            <img class="purchase-history-image" src="{{ $item->product?->image ? asset('images/'.$item->product->image) : asset('images/riwayat/paracetamol.png') }}" alt="{{ $item->product_name }}">
                            <div class="purchase-history-details">
                                <span class="purchase-history-category">{{ $item->product?->category ?? 'PRODUK' }}</span>
                                <h3>{{ $item->product_name }}</h3>
                                <p>{{ $item->product_package }} × {{ $item->quantity }}</p>
                                <strong>Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                        <time datetime="{{ $order->created_at->toDateString() }}">{{ $order->created_at->translatedFormat('d F Y') }}</time>
                        @if ($order->status === 'menunggu_pembayaran')
                            <button type="button" class="purchase-pay-trigger" data-show-qr>Bayar</button>
                            <button type="button" class="purchase-cancel-trigger" data-cancel-order-id="{{ $order->id }}">Batalkan Pesanan</button>
                        @endif
                        @if ($order->status === 'selesai')
                            @foreach ($order->items as $item)
                                <button type="button" class="purchase-review-trigger" data-review-item-id="{{ $item->id }}" data-review-name="{{ $item->product_name }}">Beri Penilaian</button>
                            @endforeach
                        @endif
                    </article>
                @empty
                    <p class="history-empty">Belum ada transaksi pembelian.</p>
                @endforelse
            </section>

            <section class="history-panel consultation-history-panel" id="consultations-panel" role="tabpanel" aria-labelledby="consultations-tab" hidden>
                <div class="consultation-history-list">
                    @forelse ($consultations as $consultation)
                        @php($isCS = $consultation->doctor_name === 'Customer Service')
                        @php($doctorSlug = ['Dr. Jon D Ojan' => 'jon-d-ojan', 'Ir. Dr. Syarif' => 'syarif', 'Dr. Fatir D H' => 'fatir-d-h'][$consultation->doctor_name] ?? 'syarif')
                        @php($doctorImage = ['Dr. Jon D Ojan' => 'dr-jon.png', 'Ir. Dr. Syarif' => 'dr-syarif.png', 'Dr. Fatir D H' => 'dr-fatir.png'][$consultation->doctor_name] ?? 'dr-syarif.png')
                        @php($chatUrl = $isCS ? route('customer-service.chat', ['consultation' => $consultation->id]) : route('konsultasi.chat', $doctorSlug).'?consultation='.$consultation->id)
                        @php($avatarSrc = $isCS ? asset('images/support.png') : asset('images/konsultasi/'.$doctorImage))
                        <a class="consultation-history-card" href="{{ $chatUrl }}" aria-label="Buka konsultasi dengan {{ $consultation->doctor_name }}">
                            <img class="consultation-history-avatar" src="{{ $avatarSrc }}" alt="" @if($isCS) style="object-fit: contain; padding: 3px; background: rgba(30, 45, 99, 0.1);" @endif>
                            <h2>{{ $consultation->doctor_name }}</h2>
                            <p class="consultation-history-specialty">{{ $isCS ? 'Admin Support' : 'Dokter Umum' }}</p>
                            <p class="consultation-history-time">{{ $consultation->updated_at->format('H.i') }} WITA</p>
                            <div class="consultation-history-status consultation-history-status--sent">
                                <span class="consultation-status-dot" aria-hidden="true"></span>
                                <span>{{ ucfirst(str_replace('_', ' ', $consultation->status)) }}</span>
                            </div>
                            <p class="consultation-history-message">{{ $consultation->messages->first()?->message ?? $consultation->complaint }}</p>
                        </a>
                    @empty
                        <p class="history-empty">Belum ada riwayat konsultasi.</p>
                    @endforelse
                </div>
            </section>
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
            <a class="nav-tab" href="{{ route('konsultasi') }}">
                <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M5 5h26v21H15l-9 7z" /></svg>
                <span>Konsultasi</span>
            </a>
            <a class="nav-tab active" href="{{ route('riwayat') }}" aria-current="page">
                <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M3 6c6-2 11-1 15 2 4-3 9-4 15-2v24c-6-2-11-1-15 2-4-3-9-4-15-2zM18 8v24" /></svg>
                <span>Riwayat</span>
            </a>
            <a class="nav-tab" href="{{ route('profil') }}">
                <svg class="profile-icon" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="11" r="6" /><path d="M5 32a13 13 0 0 1 26 0z" /></svg>
                <span>Profil</span>
            </a>
        </nav>
    <dialog class="qr-payment-dialog" id="qr-dialog" aria-labelledby="qr-dialog-title">
        <div class="qr-payment-card">
            <button type="button" class="qr-close-btn" id="close-qr-dialog" aria-label="Tutup pembayaran">&times;</button>
            <div class="qr-image-container">
                <img id="qr-payment-img" src="{{ asset('images/qris-pembayaran.png') }}" alt="QRIS Apotek WijayaFarma">
            </div>
            <button type="button" class="qr-download-btn" id="download-qr-btn">Unduh Gambar</button>
        </div>
    </dialog>

    <dialog class="history-review-dialog" id="history-review-dialog" aria-labelledby="history-review-title">
        <form id="history-review-form">
            <h2 id="history-review-title">Beri Penilaian</h2>
            <p id="history-review-product"></p>
            <input type="hidden" name="order_item_id" id="history-review-item-id">
            <label for="history-review-rating">Rating</label>
            <select id="history-review-rating" name="rating" required>
                <option value="5">5 bintang</option><option value="4">4 bintang</option><option value="3">3 bintang</option><option value="2">2 bintang</option><option value="1">1 bintang</option>
            </select>
            <label for="history-review-comment">Komentar (opsional)</label>
            <textarea id="history-review-comment" name="comment" rows="3" maxlength="3000"></textarea>
            <button type="submit">Kirim Penilaian</button>
            <button type="button" id="close-history-review">Tutup</button>
        </form>
    </dialog>

    <script>
        const historyTabs = [...document.querySelectorAll('.history-tab')];
        const historyPanels = [...document.querySelectorAll('.history-panel')];

        historyTabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                historyTabs.forEach((item, itemIndex) => {
                    const selected = itemIndex === index;
                    item.classList.toggle('is-selected', selected);
                    item.setAttribute('aria-selected', String(selected));
                });
                historyPanels.forEach((panel, panelIndex) => { panel.hidden = panelIndex !== index; });
            });
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const reviewDialog = document.querySelector('#history-review-dialog');
        document.querySelectorAll('[data-review-item-id]').forEach((button) => button.addEventListener('click', () => {
            document.querySelector('#history-review-item-id').value = button.dataset.reviewItemId;
            document.querySelector('#history-review-product').textContent = button.dataset.reviewName;
            reviewDialog.showModal();
        }));
        document.querySelector('#close-history-review').addEventListener('click', () => reviewDialog.close());
        document.querySelector('#history-review-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const values = Object.fromEntries(new FormData(event.currentTarget).entries());
            const response = await fetch('{{ route('client.reviews.store') }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(values),
            });
            const result = await response.json();
            window.alert(result.message || (response.ok ? 'Ulasan tersimpan.' : 'Ulasan gagal dikirim.'));
            if (response.ok) reviewDialog.close();
        });
        document.querySelectorAll('[data-cancel-order-id]').forEach((button) => button.addEventListener('click', async () => {
            if (!window.confirm('Batalkan pesanan ini?')) return;
            const response = await fetch(`{{ url('/client/orders') }}/${button.dataset.cancelOrderId}/cancel`, {
                method: 'PATCH', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const result = await response.json();
            window.alert(result.message || 'Pesanan belum berhasil dibatalkan.');
            if (response.ok) window.location.reload();
        }));

        const qrDialog = document.querySelector('#qr-dialog');
        document.querySelectorAll('[data-show-qr]').forEach((btn) => {
            btn.addEventListener('click', () => qrDialog.showModal());
        });
        document.querySelector('#close-qr-dialog').addEventListener('click', () => {
            qrDialog.close();
        });
        document.querySelector('#download-qr-btn').addEventListener('click', async () => {
            const qrImg = document.querySelector('#qr-payment-img');
            const imageUrl = qrImg.src;
            try {
                const res = await fetch(imageUrl);
                const blob = await res.blob();
                const blobUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = 'QRIS-Pembayaran-WijayaFarma.png';
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(blobUrl);
            } catch (e) {
                const link = document.createElement('a');
                link.href = imageUrl;
                link.download = 'QRIS-Pembayaran-WijayaFarma.png';
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                link.remove();
            }
        });
    </script>
</body>
</html>
