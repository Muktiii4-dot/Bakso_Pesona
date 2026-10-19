<?php

namespace Database\Seeders;

use App\Models\category;
use App\Models\Product;
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
                'name' => 'Bakso Malang Original',
                'slug' => 'bakso-malang-original',
                'category' => 'bakso',
                'description' => 'Bakso Malang dengan perpaduan bakso, tahu, siomay, mie, sayuran, dan kuah gurih khas Bakso Pesona.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Best Seller',
                'is_available' => true,
            ],
            [
                'name' => 'Bakso Pedas',
                'slug' => 'bakso-pedas',
                'category' => 'bakso',
                'description' => 'Bakso dengan kuah pedas yang kaya rasa untuk kamu yang menyukai sensasi pedas.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Pedas',
                'is_available' => true,
            ],
            [
                'name' => 'Bakso Keju',
                'slug' => 'bakso-keju',
                'category' => 'bakso',
                'description' => 'Bakso dengan isian keju yang lembut dan gurih, cocok untuk pecinta perpaduan rasa unik.',
                'price' => 30000,
                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Favorit',
                'is_available' => true,
            ],
            [
                'name' => 'Mie Bakso Jumbo',
                'slug' => 'mie-bakso-jumbo',
                'category' => 'mie',
                'description' => 'Mie bakso dengan ukuran jumbo dan porsi yang mengenyangkan untuk menemani waktu makanmu.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1565123409695-7b5ef63a7a6d?auto=format&fit=crop&w=900&q=80',
                'badge' => null,
                'is_available' => true,
            ],
            [
                'name' => 'Bakso Mercon',
                'slug' => 'bakso-mercon',
                'category' => 'bakso',
                'description' => 'Bakso dengan cita rasa pedas dan bumbu khas yang memberikan sensasi berbeda.',
                'price' => 28000,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Hot',
                'is_available' => true,
            ],
            [
                'name' => 'Pangsit Goreng',
                'slug' => 'pangsit-goreng',
                'category' => 'cemilan',
                'description' => 'Pangsit goreng renyah yang cocok dijadikan teman makan bakso.',
                'price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1563227812-0ea4c22e6cc8?auto=format&fit=crop&w=900&q=80',
                'badge' => null,
                'is_available' => true,
            ],
            [
                'name' => 'Siomay Goreng',
                'slug' => 'siomay-goreng',
                'category' => 'cemilan',
                'description' => 'Siomay goreng dengan tekstur renyah di luar dan lembut di dalam.',
                'price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=80',
                'badge' => null,
                'is_available' => true,
            ],
            [
                'name' => 'Paket Hemat Pesona',
                'slug' => 'paket-hemat-pesona',
                'category' => 'paket',
                'description' => 'Paket hemat berisi menu pilihan Bakso Pesona untuk makan sendiri atau bersama teman.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Hemat',
                'is_available' => true,
            ],
        ];

        foreach ($products as $item) {
            $categoryId = category::where(
                'slug',
                $item['category']
            )->value('id');

            if (!$categoryId) {
                throw new \RuntimeException(
                    "Kategori '{$item['category']}' Tidak Ditemukan."
                );
            }

            Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $categoryId,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'badge' => $item['badge'],
                    'is_available' => $item['is_available'],
                ]
            );
        }
    }
}
