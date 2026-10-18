<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121f4d">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="order-confirm-page">
    <main class="order-confirm-screen">
        <header class="order-confirm-header">
            <div class="confirm-status"><strong>9:41</strong><span aria-hidden="true"><i></i><i></i><i></i><i></i><b>◔</b><em></em></span></div>
            <a href="{{ route('checkout') }}" class="confirm-back" aria-label="Kembali ke keranjang">‹</a>
            <h1>Checkout</h1>
        </header>

        <div class="order-confirm-content">
            <section class="confirm-section address-section">
                <p class="confirm-eyebrow">Delivery Address</p>
                <h2>Alamat Pengiriman</h2>
                <div class="address-card">
                    <p id="shipping-address">Pilih alamat pengiriman</p>
                    <span class="location-icon" aria-hidden="true"><i></i></span>
                    <a href="{{ route('checkout.address') }}" class="edit-address">Ubah Alamat</a>
                </div>
            </section>

            <section class="confirm-section pharmacy-section">
                <p class="confirm-eyebrow">Selected Pharmacy</p>
                <h2>Apotek Terpilih</h2>
                <div class="pharmacy-card">
                    <strong id="selected-pharmacy">Wijaya Farma, Cabang<br>Banjarmasin Tengah</strong>
                    <span>● &nbsp;Jl. Gatot Subroto No. 45, Banjarmasin</span>
                    <b>★ 4,8 (2,533) dari anda</b>
                </div>
                <fieldset class="fulfillment-options">
                    <legend>Metode pemenuhan</legend>
                    <label><input type="radio" name="fulfillment_method" value="delivery" checked> Diantar ke alamat</label>
                    <label><input type="radio" name="fulfillment_method" value="pickup"> Ambil di apotek</label>
                </fieldset>
            </section>

            <section class="confirm-section summary-section">
                <p class="confirm-eyebrow">Item Summary</p>
                <h2>Ringkasan Item</h2>
                <div class="summary-grid" id="summary-grid"></div>
                <p class="summary-empty" id="summary-empty" hidden>Keranjang masih kosong. <a href="{{ route('toko') }}">Kembali ke Toko</a></p>
            </section>

            <section class="confirm-section payment-section">
                <p class="confirm-eyebrow">Payment Method</p>
                <h2>Metode Pembayaran</h2>
                <div class="payment-card">
                    <div class="payment-options">
                        <span><i class="payment-badge ovo-badge">ovo</i>OVO</span>
                        <span><i class="payment-badge gopay-badge">G</i>GoPay</span>
                        <span><img class="payment-qris-logo" src="{{ asset('images/qris-logo.png') }}" alt="">QRIS</span>
                    </div>
                    <button type="button" id="choose-payment">Pilih Metode</button>
                </div>
            </section>

            <section class="confirm-total">
                <div><p class="confirm-eyebrow">Total Payment</p><span>Total Pembayaran</span></div>
                <strong id="order-total">Rp 0</strong>
            </section>

            <button class="place-order-button" type="button" id="place-order">Buat Pesanan</button>
        </div>
        <div class="home-indicator" aria-hidden="true"></div>
    </main>

    <dialog class="checkout-action-dialog" id="payment-dialog" aria-labelledby="payment-dialog-title">
        <h2 id="payment-dialog-title">Pilih Metode Pembayaran</h2>
        <button type="button" class="payment-choice" data-payment="OVO"><i class="payment-badge ovo-badge">ovo</i> OVO <span>›</span></button>
        <button type="button" class="payment-choice" data-payment="GoPay"><i class="payment-badge gopay-badge">G</i> GoPay <span>›</span></button>
        <button type="button" class="payment-choice" data-payment="QRIS"><img class="payment-qris-logo" src="{{ asset('images/qris-logo.png') }}" alt=""> QRIS <span>›</span></button>
        <button type="button" class="action-dialog-cancel" data-close-payment>Tutup</button>
    </dialog>
    <dialog class="qr-payment-dialog" id="qr-dialog" aria-labelledby="qr-dialog-title">
        <div class="qr-payment-card">
            <button type="button" class="qr-close-btn" id="close-qr-dialog" aria-label="Tutup pembayaran">&times;</button>
            <div class="qr-image-container">
                <img id="qr-payment-img" src="{{ asset('images/qris-pembayaran.png') }}" alt="QRIS Apotek WijayaFarma">
            </div>
            <button type="button" class="qr-download-btn" id="download-qr-btn">Unduh Gambar</button>
        </div>
    </dialog>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let orderCart = [];
        let paymentMethod = '';
        let selectedAddressId = null;
        const formatRupiah = (amount) => `Rp ${new Intl.NumberFormat('id-ID').format(amount)}`;
        const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char]);
        const renderSummary = () => {
            const total = orderCart.reduce((sum, item) => sum + (Number(item.product.price) * Number(item.quantity)), 0);
            document.querySelector('#order-total').textContent = formatRupiah(total);
            document.querySelector('#summary-empty').hidden = orderCart.length > 0;
            document.querySelector('#summary-grid').innerHTML = orderCart.map((item) => `
                <article class="summary-item">
                    <img src="{{ asset('images') }}/${escapeHtml(item.product.image || '')}" alt="${escapeHtml(item.product.name)}">
                    <div><strong>${escapeHtml(item.product.name)}</strong><span>x${Number(item.quantity)}</span></div>
                    <b>${formatRupiah(Number(item.product.price) * Number(item.quantity))}</b>
                </article>`).join('');
        };
        const loadCheckout = async () => {
            const [cartResponse, profileResponse] = await Promise.all([
                fetch('{{ route('client.cart.index') }}', { headers: { 'Accept': 'application/json' } }),
                fetch('{{ route('client.profile.show') }}', { headers: { 'Accept': 'application/json' } }),
            ]);
            const cartData = await cartResponse.json();
            const profileData = await profileResponse.json();
            const addressResponse = await fetch('{{ route('client.addresses.index') }}', { headers: { 'Accept': 'application/json' } });
            const addressData = addressResponse.ok ? await addressResponse.json() : { addresses: [] };
            const prescriptionResponse = await fetch('{{ route('client.prescriptions.index') }}', { headers: { 'Accept': 'application/json' } });
            const prescriptionData = prescriptionResponse.ok ? await prescriptionResponse.json() : { prescriptions: [] };
            if (cartResponse.ok) orderCart = cartData.items;
            const selectedAddress = addressData.addresses.find((address) => address.is_default) || addressData.addresses[0];
            if (selectedAddress) {
                selectedAddressId = selectedAddress.id;
                document.querySelector('#shipping-address').textContent = `${selectedAddress.label}: ${selectedAddress.address}`;
            } else if (profileResponse.ok && profileData.client.address) {
                document.querySelector('#shipping-address').textContent = profileData.client.address;
            }
            window.availablePrescriptionIds = prescriptionData.prescriptions.map((prescription) => prescription.id);
            renderSummary();
        };
        const paymentDialog = document.querySelector('#payment-dialog');
        const qrDialog = document.querySelector('#qr-dialog');
        document.querySelector('#choose-payment').addEventListener('click', () => paymentDialog.showModal());
        document.querySelector('[data-close-payment]').addEventListener('click', () => paymentDialog.close());
        document.querySelectorAll('[data-payment]').forEach((button) => button.addEventListener('click', () => {
            paymentMethod = button.dataset.payment;
            document.querySelector('#choose-payment').textContent = paymentMethod;
            paymentDialog.close();
        }));
        document.querySelectorAll('[name="fulfillment_method"]').forEach((input) => input.addEventListener('change', () => {
            document.querySelector('.address-section').hidden = input.value === 'pickup';
            document.querySelector('.pharmacy-section').classList.toggle('is-pickup-selected', input.value === 'pickup');
        }));
        document.querySelector('#place-order').addEventListener('click', async () => {
            if (!orderCart.length) { window.location.href = '{{ route('toko') }}'; return; }
            if (!paymentMethod) { paymentDialog.showModal(); return; }
            if (document.querySelector('[name="fulfillment_method"]:checked').value === 'delivery' && !selectedAddressId) {
                window.alert('Tambahkan alamat pengiriman terlebih dahulu.');
                window.location.href = '{{ route('checkout.address') }}';
                return;
            }
            const placeOrderButton = document.querySelector('#place-order');
            placeOrderButton.disabled = true;
            try {
                const response = await fetch('{{ route('client.orders.store') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({
                        fulfillment_method: document.querySelector('[name="fulfillment_method"]:checked').value,
                        shipping_address: document.querySelector('#shipping-address').textContent.replace(/\s+/g, ' ').trim(),
                        saved_address_id: selectedAddressId,
                        pharmacy_branch: localStorage.getItem('wijaya-pharmacy-branch') || 'Banjarmasin Tengah',
                        payment_method: paymentMethod,
                        prescription_ids: window.availablePrescriptionIds || [],
                    }),
                });
                const result = await response.json();
                if (!response.ok) { window.alert(result.message || 'Pesanan belum berhasil dibuat.'); return; }
                orderCart = [];
                qrDialog.showModal();
            } catch (error) {
                window.alert('Gagal menghubungi server. Coba lagi.');
            } finally {
                placeOrderButton.disabled = false;
            }
        });
        document.querySelector('#close-qr-dialog').addEventListener('click', () => {
            qrDialog.close();
            window.location.href = '{{ route('riwayat') }}';
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
        const selectedBranch = localStorage.getItem('wijaya-pharmacy-branch');
        if (selectedBranch) document.querySelector('#selected-pharmacy').innerHTML = `Wijaya Farma, Cabang<br>${escapeHtml(selectedBranch)}`;
        loadCheckout().catch(() => { document.querySelector('#summary-empty').hidden = false; });
    </script>
</body>
</html>
