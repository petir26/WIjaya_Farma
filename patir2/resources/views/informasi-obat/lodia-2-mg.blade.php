@php
    $medicine = [
        'name' => 'Lodia 2 mg', 'display_name' => 'Lodia 2 mg', 'category' => 'PENCERNAAN',
        'package' => 'Strip 10 Tablet', 'price' => 'Rp 27.300', 'image' => 'obat-lodia.png',
        'content' => 'Loperamide 2 mg', 'dose' => ['Ikuti petunjuk pada kemasan.'],
        'description' => 'Obat untuk membantu mengatasi diare.',
        'warning' => 'Gunakan sesuai petunjuk pada kemasan.',
    ];
@endphp
@include('informasi-obat.show')
