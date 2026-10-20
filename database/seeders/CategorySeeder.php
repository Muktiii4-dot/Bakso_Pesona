<?php

namespace Database\Seeders;

use App\Models\category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bakso',
                'slug' => 'bakso',
                'description' => 'Beragam pilihan bakso Bakso Pesona.',
            ],
            [
                'name' => 'Mie',
                'slug' => 'mie',
                'description' => 'Pilihan menu mie yang menggugah selera.',
            ],
            [
                'name' => 'Cemilan',
                'slug' => 'cemilan',
                'description' => 'Cemilan pendamping untuk menikmati bakso.',
            ],
            [
                'name' => 'Paket',
                'slug' => 'paket',
                'description' => 'Paket menu pilihan Bakso Pesona.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
