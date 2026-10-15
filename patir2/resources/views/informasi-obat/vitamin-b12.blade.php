@php
    $medicine = [
        'name' => 'Vitamin B12', 'display_name' => 'Vitamin B12', 'category' => 'VITAMIN',
        'package' => 'Botol 60 Tablet', 'price' => 'Rp 65.000', 'image' => 'obat-vitamin-b12.png',
        'content' => 'Vitamin B12', 'dose' => ['Ikuti petunjuk pada kemasan.'],
        'description' => 'Suplemen vitamin B12 dalam kemasan 60 tablet.',
        'warning' => 'Gunakan sesuai petunjuk pada kemasan.',
    ];
@endphp
@include('informasi-obat.show')
