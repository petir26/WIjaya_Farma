<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'slug' => 'paracetamol', 'name' => 'Paracetamol', 'category' => 'FLU & BATUK',
                'package' => 'Strip 10 Tablet', 'price' => 12500, 'stock' => 50,
                'description' => 'Obat pereda nyeri dan penurun demam.', 'content' => 'Paracetamol 500 mg',
                'dose' => ['Dewasa: 500–1000 mg', 'Anak-anak: 10–15 mg/kg'],
                'warning' => 'Jangan digunakan berlebihan karena berisiko menyebabkan kerusakan hati.',
                'image' => 'obat-paracetamol.png', 'requires_prescription' => false,
            ],
            [
                'slug' => 'vitamin-b12', 'name' => 'Vitamin B12', 'category' => 'VITAMIN',
                'package' => 'Botol 60 Tablet', 'price' => 65000, 'stock' => 30,
                'description' => 'Suplemen vitamin B12 dalam kemasan 60 tablet.', 'content' => 'Vitamin B12',
                'dose' => ['Ikuti petunjuk pada kemasan.'], 'warning' => 'Gunakan sesuai petunjuk pada kemasan.',
                'image' => 'obat-vitamin-b12.png', 'requires_prescription' => false,
            ],
            [
                'slug' => 'vitamin-cipi', 'name' => 'Vitamin Cipi', 'category' => 'VITAMIN',
                'package' => 'Botol 45 Tablet', 'price' => 10450, 'stock' => 40,
                'description' => 'Suplemen vitamin C rasa jeruk dalam kemasan 45 tablet.', 'content' => 'Vitamin C',
                'dose' => ['Ikuti petunjuk pada kemasan.'], 'warning' => 'Gunakan sesuai petunjuk pada kemasan.',
                'image' => 'obat-vitamin-cipi.png', 'requires_prescription' => false,
            ],
            [
                'slug' => 'lodia-2-mg', 'name' => 'Lodia 2 mg', 'category' => 'PENCERNAAN',
                'package' => 'Strip 10 Tablet', 'price' => 27300, 'stock' => 25,
                'description' => 'Obat untuk membantu mengatasi diare.', 'content' => 'Loperamide 2 mg',
                'dose' => ['Ikuti petunjuk pada kemasan.'], 'warning' => 'Gunakan sesuai petunjuk pada kemasan.',
                'image' => 'obat-lodia.png', 'requires_prescription' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
