@php
    $medicine = [
        'name' => 'Paracetamol',
        'display_name' => 'Paracetamol 500 mg',
        'category' => 'FLU & BATUK',
        'package' => 'Strip 10 Tablet',
        'price' => 'Rp 12.500',
        'image' => 'obat-paracetamol.png',
        'content' => 'Paracetamol 500 mg',
        'dose' => ['<strong>Dewasa</strong> 500 - 1000 Mg', '<strong>Anak - Anak</strong> 10 - 15 mg/Kg'],
        'description' => 'Paracetamol adalah obat pereda nyeri dan penurun demam. Umumnya digunakan untuk sakit kepala, nyeri otot, sakit gigi, atau demam akibat flu.',
        'warning' => 'Jangan Berlebihan, Risiko Kerusakan Hati',
    ];
@endphp
@include('informasi-obat.show')
