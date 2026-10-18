<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b4e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Alamat Pengiriman | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="address-picker-page">
    <main class="address-picker-screen" id="address-picker-screen">
        <div class="address-map" aria-label="Peta untuk memilih lokasi pengiriman">
            <img class="address-map-image" id="address-map-fallback" src="{{ asset('images/pharmacy-map.png') }}" alt="Peta lokasi untuk referensi">
            <div id="address-google-map" aria-label="Peta Google interaktif"></div>
            <img class="address-map-pin" id="address-map-fallback-pin" src="{{ asset('images/map-pin.png') }}" alt="Penanda lokasi pilihan">
        </div>
        <header class="address-picker-header">
            <div class="confirm-status"><strong>9:41</strong><span aria-hidden="true"><i></i><i></i><i></i><i></i><b>◔</b><em></em></span></div>
            <a href="{{ route('checkout.confirmation') }}" class="address-picker-back" aria-label="Kembali ke konfirmasi">‹</a>
            <h1>Alamat Pengiriman</h1>
        </header>

        <div class="address-map-search" id="address-map-search">
            <div id="place-autocomplete-slot"></div>
            @unless ($mapsApiKey)
                <div class="address-manual-search">
                    <input id="manual-map-search" type="search" placeholder="Cari alamat di Google Maps" aria-label="Cari alamat di Google Maps">
                    <button type="button" id="open-google-maps">Cari</button>
                </div>
            @endunless
        </div>

        <section class="pharmacy-sheet address-book-sheet" aria-label="Alamat tersimpan">
            <button class="sheet-handle" id="address-sheet-handle" type="button" aria-label="Geser panel alamat" aria-controls="saved-address-content" aria-expanded="false"></button>
            <div class="address-book-content" id="saved-address-content">
                <div class="address-book-heading">
                    <div>
                        <h2>Alamat Saya</h2>
                        <p>Pilih alamat atau tambahkan bookmark baru</p>
                    </div>
                    <button type="button" class="address-add-trigger" id="open-address-form" aria-label="Tambah alamat">+</button>
                </div>
                @if (!$mapsApiKey)
                    <p class="maps-key-notice">Peta interaktif aktif setelah Google Maps API key diatur. Untuk sementara alamat tetap bisa ditambahkan manual.</p>
                @else
                    <p class="address-map-hint" id="address-map-hint">Cari alamat atau ketuk peta untuk menentukan titik pengiriman.</p>
                @endif

                <div class="saved-address-list" id="saved-address-list">
                    @forelse ($addresses as $address)
                        <article class="saved-address-card {{ $address->is_default ? 'is-default' : '' }}" data-saved-address-id="{{ $address->id }}">
                            <button type="button" class="saved-address-select" data-select-address="{{ $address->id }}">
                                <span class="saved-address-title"><strong>{{ $address->label }}</strong>@if($address->is_default)<em>Dipilih</em>@endif</span>
                                <span class="saved-address-meta">{{ $address->recipient_name }} · {{ $address->phone }}</span>
                                <span class="saved-address-text">{{ $address->address }}</span>
                                @if ($address->notes)<span class="saved-address-note">Catatan: {{ $address->notes }}</span>@endif
                            </button>
                            <button type="button" class="saved-address-delete" data-delete-address="{{ $address->id }}" aria-label="Hapus alamat {{ $address->label }}">Hapus</button>
                        </article>
                    @empty
                        <p class="saved-address-empty">Belum ada alamat tersimpan. Tambahkan alamat pengiriman pertama Anda.</p>
                    @endforelse
                </div>

                <button type="button" class="address-book-add-button" id="open-address-form-bottom">+ Tambah alamat baru</button>
            </div>
        </section>
    </main>

    <dialog class="saved-address-dialog" id="saved-address-dialog" aria-labelledby="saved-address-dialog-title">
        <form id="saved-address-form">
            <button type="button" class="saved-address-dialog-close" id="close-address-form" aria-label="Tutup">×</button>
            <h2 id="saved-address-dialog-title">Simpan alamat</h2>
            <p class="saved-address-dialog-help">Pilih titik pada peta atau cari alamat, lalu beri nama bookmark agar mudah dipakai lagi.</p>
            <label for="address-label">Simpan sebagai</label>
            <select name="label" id="address-label" required>
                <option value="Rumah">Rumah</option>
                <option value="Kantor">Kantor</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            <label for="recipient-name">Nama penerima</label>
            <input name="recipient_name" id="recipient-name" value="{{ $client->name }}" maxlength="120" required>
            <label for="recipient-phone">Nomor telepon</label>
            <input name="phone" id="recipient-phone" value="{{ $client->phone }}" type="tel" maxlength="30" required>
            <label for="delivery-address">Alamat lengkap</label>
            <textarea name="address" id="delivery-address" rows="3" maxlength="2000" placeholder="Pilih titik pada peta atau tulis alamat lengkap" required></textarea>
            <input type="hidden" name="place_id" id="address-place-id">
            <input type="hidden" name="latitude" id="address-latitude">
            <input type="hidden" name="longitude" id="address-longitude">
            <label for="address-notes">Catatan untuk kurir (opsional)</label>
            <input name="notes" id="address-notes" maxlength="500" placeholder="Contoh: rumah pagar hitam">
            <button type="submit" class="address-save-button" id="save-address-button">Simpan dan pilih alamat</button>
            <p class="address-form-error" id="address-form-error" role="alert" hidden></p>
        </form>
    </dialog>

    <script>
        const addressCsrf = document.querySelector('meta[name="csrf-token"]').content;
        const addressApi = '{{ route('client.addresses.index') }}';
        const addressStoreApi = '{{ route('client.addresses.store') }}';
        const confirmationUrl = '{{ route('checkout.confirmation') }}';
        const addressDialog = document.querySelector('#saved-address-dialog');
        const addressForm = document.querySelector('#saved-address-form');
        const addressField = document.querySelector('#delivery-address');
        const apiConfigured = @json((bool) $mapsApiKey);
        let chosenPlace = null;
        let googleMap = null;
        let addressMarker = null;
        let geocoder = null;

        const addressSheet = document.querySelector('.address-book-sheet');
        const addressSheetHandle = document.querySelector('#address-sheet-handle');
        const collapsedSheetHeight = 300;
        const expandedSheetHeight = () => Math.min(window.innerHeight * 0.82, 700);
        let sheetDrag = null;
        let ignoreSheetClick = false;

        const setAddressSheetHeight = (height) => {
            const max = expandedSheetHeight();
            const next = Math.max(Math.min(collapsedSheetHeight, max), Math.min(max, height));
            addressSheet.style.height = `${next}px`;
            addressSheetHandle.setAttribute('aria-expanded', String(next > (collapsedSheetHeight + max) / 2));
        };

        addressSheetHandle.addEventListener('pointerdown', (event) => {
            sheetDrag = { startY: event.clientY, startHeight: addressSheet.getBoundingClientRect().height, moved: false };
            addressSheet.classList.add('is-dragging');
            addressSheetHandle.setPointerCapture(event.pointerId);
        });
        addressSheetHandle.addEventListener('pointermove', (event) => {
            if (!sheetDrag) return;
            const distance = sheetDrag.startY - event.clientY;
            if (Math.abs(distance) > 5) sheetDrag.moved = true;
            if (sheetDrag.moved) {
                event.preventDefault();
                setAddressSheetHeight(sheetDrag.startHeight + distance);
            }
        });
        const finishSheetDrag = () => {
            if (!sheetDrag) return;
            if (sheetDrag.moved) {
                const max = expandedSheetHeight();
                const current = addressSheet.getBoundingClientRect().height;
                setAddressSheetHeight(current > (collapsedSheetHeight + max) / 2 ? max : collapsedSheetHeight);
                ignoreSheetClick = true;
                window.setTimeout(() => { ignoreSheetClick = false; }, 250);
            }
            sheetDrag = null;
            addressSheet.classList.remove('is-dragging');
        };
        addressSheetHandle.addEventListener('pointerup', finishSheetDrag);
        addressSheetHandle.addEventListener('pointercancel', finishSheetDrag);
        addressSheetHandle.addEventListener('click', (event) => {
            if (ignoreSheetClick) { ignoreSheetClick = false; event.preventDefault(); return; }
            const max = expandedSheetHeight();
            const current = addressSheet.getBoundingClientRect().height;
            setAddressSheetHeight(current > (collapsedSheetHeight + max) / 2 ? collapsedSheetHeight : max);
        });

        const openAddressForm = () => {
            document.querySelector('#address-form-error').hidden = true;
            addressDialog.showModal();
        };
        document.querySelector('#open-address-form').addEventListener('click', openAddressForm);
        document.querySelector('#open-address-form-bottom').addEventListener('click', openAddressForm);
        document.querySelector('#close-address-form').addEventListener('click', () => addressDialog.close());

        document.querySelectorAll('[data-select-address]').forEach((button) => button.addEventListener('click', async () => {
            button.disabled = true;
            const response = await fetch(`${addressApi}/${button.dataset.selectAddress}/select`, {
                method: 'PATCH', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': addressCsrf },
            });
            if (response.ok) window.location.href = confirmationUrl;
            else { button.disabled = false; window.alert('Alamat belum berhasil dipilih. Coba lagi.'); }
        }));

        document.querySelectorAll('[data-delete-address]').forEach((button) => button.addEventListener('click', async () => {
            if (!window.confirm('Hapus bookmark alamat ini?')) return;
            const response = await fetch(`${addressApi}/${button.dataset.deleteAddress}`, {
                method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': addressCsrf },
            });
            const result = await response.json();
            if (response.ok) window.location.reload();
            else window.alert(result.message || 'Alamat belum berhasil dihapus.');
        }));

        addressForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submitButton = document.querySelector('#save-address-button');
            const errorMessage = document.querySelector('#address-form-error');
            const formValues = Object.fromEntries(new FormData(addressForm).entries());
            formValues.is_default = true;
            Object.keys(formValues).forEach((key) => { if (formValues[key] === '') formValues[key] = null; });
            submitButton.disabled = true;
            errorMessage.hidden = true;
            try {
                const response = await fetch(addressStoreApi, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': addressCsrf },
                    body: JSON.stringify(formValues),
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat()[0] || 'Alamat belum berhasil disimpan.');
                window.location.href = confirmationUrl;
            } catch (error) {
                errorMessage.textContent = error.message || 'Alamat belum berhasil disimpan.';
                errorMessage.hidden = false;
                submitButton.disabled = false;
            }
        });

        if (!apiConfigured) {
            document.querySelector('#open-google-maps').addEventListener('click', () => {
                const query = document.querySelector('#manual-map-search').value.trim();
                if (!query) { window.alert('Masukkan nama tempat atau alamat terlebih dahulu.'); return; }
                window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`, '_blank', 'noopener');
                document.querySelector('#address-label').value = 'Rumah';
                addressField.value = query;
                openAddressForm();
            });
        }

        window.initAddressMap = async () => {
            try {
                const [{ Map }, { PlaceAutocompleteElement }] = await Promise.all([
                    google.maps.importLibrary('maps'),
                    google.maps.importLibrary('places'),
                ]);
                const initial = @json($addresses->firstWhere('is_default', true));
                const initialCenter = initial?.latitude && initial?.longitude
                    ? { lat: Number(initial.latitude), lng: Number(initial.longitude) }
                    : { lat: -3.3186, lng: 114.5944 };
                googleMap = new Map(document.querySelector('#address-google-map'), {
                    center: initialCenter,
                    zoom: initial ? 16 : 13,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                    clickableIcons: false,
                });
                geocoder = new google.maps.Geocoder();
                addressMarker = new google.maps.Marker({ map: googleMap, position: initial ? initialCenter : null, draggable: true });
                document.querySelector('#address-map-fallback').hidden = true;
                document.querySelector('#address-map-fallback-pin').hidden = true;
                document.querySelector('#address-picker-screen')?.classList.add('has-google-map');

                const autocomplete = new PlaceAutocompleteElement();
                autocomplete.placeholder = 'Cari nama tempat atau alamat';
                autocomplete.includedRegionCodes = ['id'];
                autocomplete.locationBias = { center: initialCenter, radius: 50000 };
                document.querySelector('#place-autocomplete-slot').append(autocomplete);

                const setPlace = (position, address, placeId = null) => {
                    googleMap.setCenter(position);
                    googleMap.setZoom(17);
                    addressMarker.setPosition(position);
                    chosenPlace = { ...position, place_id: placeId };
                    addressField.value = address || `${position.lat}, ${position.lng}`;
                    document.querySelector('#address-latitude').value = position.lat;
                    document.querySelector('#address-longitude').value = position.lng;
                    document.querySelector('#address-place-id').value = placeId || '';
                };

                if (initial) {
                    chosenPlace = { ...initialCenter, place_id: initial.place_id };
                    document.querySelector('#address-map-hint').textContent = `Alamat terpilih: ${initial.address}`;
                }
                autocomplete.addEventListener('gmp-select', async ({ placePrediction }) => {
                    const place = placePrediction.toPlace();
                    await place.fetchFields({ fields: ['formattedAddress', 'location', 'viewport', 'id'] });
                    if (!place.location) return;
                    if (place.viewport) googleMap.fitBounds(place.viewport);
                    else { googleMap.setCenter(place.location); googleMap.setZoom(17); }
                    setPlace({ lat: place.location.lat(), lng: place.location.lng() }, place.formattedAddress, place.id);
                    document.querySelector('#address-map-hint').textContent = `Alamat terpilih: ${place.formattedAddress}`;
                });

                googleMap.addListener('click', async ({ latLng }) => {
                    const position = { lat: latLng.lat(), lng: latLng.lng() };
                    addressMarker.setPosition(position);
                    chosenPlace = { ...position, place_id: null };
                    document.querySelector('#address-latitude').value = position.lat;
                    document.querySelector('#address-longitude').value = position.lng;
                    document.querySelector('#address-place-id').value = '';
                    try {
                        const result = await geocoder.geocode({ location: position });
                        const formattedAddress = result.results[0]?.formatted_address || `${position.lat.toFixed(6)}, ${position.lng.toFixed(6)}`;
                        addressField.value = formattedAddress;
                        document.querySelector('#address-map-hint').textContent = `Alamat terpilih: ${formattedAddress}`;
                    } catch {
                        addressField.value = `${position.lat.toFixed(6)}, ${position.lng.toFixed(6)}`;
                    }
                });

                addressMarker.addListener('dragend', async () => {
                    const position = addressMarker.getPosition();
                    if (!position) return;
                    const point = { lat: position.lat(), lng: position.lng() };
                    chosenPlace = { ...point, place_id: null };
                    document.querySelector('#address-latitude').value = point.lat;
                    document.querySelector('#address-longitude').value = point.lng;
                    document.querySelector('#address-place-id').value = '';
                    try {
                        const result = await geocoder.geocode({ location: point });
                        addressField.value = result.results[0]?.formatted_address || `${point.lat}, ${point.lng}`;
                    } catch { addressField.value = `${point.lat.toFixed(6)}, ${point.lng.toFixed(6)}`; }
                    document.querySelector('#address-map-hint').textContent = `Alamat terpilih: ${addressField.value}`;
                });

                addressField.addEventListener('input', () => { if (!addressField.value.trim()) chosenPlace = null; });
            } catch (error) {
                document.querySelector('#address-map-hint').textContent = 'Peta gagal dimuat. Masukkan alamat secara manual.';
            }
        };
    </script>
    @if ($mapsApiKey)
        <script async src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($mapsApiKey) }}&v=weekly&libraries=places&callback=initAddressMap"></script>
    @endif
</body>
</html>
