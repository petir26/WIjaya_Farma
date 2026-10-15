@php
    $medicine = [
        'name' => 'Vitamin Cipi', 'display_name' => 'Vitamin Cipi', 'category' => 'VITAMIN',
        'package' => 'Botol 45 Tablet', 'price' => 'Rp 10.450', 'image' => 'obat-vitamin-cipi.png',
        'content' => 'Vitamin C', 'dose' => ['Ikuti petunjuk pada kemasan.'],
        'description' => 'Suplemen vitamin C rasa jeruk dalam kemasan 45 tablet.',
        'warning' => 'Gunakan sesuai petunjuk pada kemasan.',
    ];
@endphp
@include('informasi-obat.show')
