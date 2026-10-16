<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Data produk sementara.
     *
     * Nanti data ini akan dipindahkan
     * ke database pada BAB 6.
     */
    private function products(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Bakso Malang Original',
                'slug' => 'bakso-malang-original',
                'category' => 'Bakso',
                'description' => 'Bakso Malang dengan perpaduan bakso, tahu, siomay, mie, sayuran, dan kuah gurih khas Bakso Pesona.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Best Seller',
                'is_available' => true,
            ],

            [
                'id' => 2,
                'name' => 'Bakso Pedas',
                'slug' => 'bakso-pedas',
                'category' => 'Bakso',
                'description' => 'Bakso dengan kuah pedas yang kaya rasa untuk kamu yang menyukai sensasi pedas.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Pedas',
                'is_available' => true,
            ],

            [
                'id' => 3,
                'name' => 'Bakso Keju',
                'slug' => 'bakso-keju',
                'category' => 'Bakso',
                'description' => 'Bakso dengan isian keju yang lembut dan gurih, cocok untuk pecinta perpaduan rasa unik.',
                'price' => 30000,
                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Favorit',
                'is_available' => true,
            ],

            [
                'id' => 4,
                'name' => 'Mie Bakso Jumbo',
                'slug' => 'mie-bakso-jumbo',
                'category' => 'Mie',
                'description' => 'Mie bakso dengan ukuran jumbo dan porsi yang mengenyangkan untuk menemani waktu makanmu.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1565123409695-7b5ef63a7a6d?auto=format&fit=crop&w=900&q=80',
                'badge' => null,
                'is_available' => true,
            ],

            [
                'id' => 5,
                'name' => 'Bakso Mercon',
                'slug' => 'bakso-mercon',
                'category' => 'Bakso',
                'description' => 'Bakso dengan cita rasa pedas dan bumbu khas yang memberikan sensasi berbeda.',
                'price' => 28000,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Hot',
                'is_available' => true,
            ],

            [
                'id' => 6,
                'name' => 'Pangsit Goreng',
                'slug' => 'pangsit-goreng',
                'category' => 'Cemilan',
                'description' => 'Pangsit goreng renyah yang cocok dijadikan teman makan bakso.',
                'price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1563227812-0ea4c22e6cc8?auto=format&fit=crop&w=900&q=80',
                'badge' => null,
                'is_available' => true,
            ],

            [
                'id' => 7,
                'name' => 'Siomay Goreng',
                'slug' => 'siomay-goreng',
                'category' => 'Cemilan',
                'description' => 'Siomay goreng dengan tekstur renyah di luar dan lembut di dalam.',
                'price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=80',
                'badge' => null,
                'is_available' => true,
            ],

            [
                'id' => 8,
                'name' => 'Paket Hemat Pesona',
                'slug' => 'paket-hemat-pesona',
                'category' => 'Paket',
                'description' => 'Paket hemat berisi menu pilihan Bakso Pesona untuk makan sendiri atau bersama teman.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=80',
                'badge' => 'Hemat',
                'is_available' => true,
            ],
        ];
    }


    /**
     * Menampilkan seluruh produk.
     */
    public function index(Request $request)
    {
        $products = collect($this->products());


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = trim($request->query('q', ''));

        if ($search !== '') {

            $products = $products->filter(function ($product) use ($search) {

                return str_contains(
                    strtolower($product['name']),
                    strtolower($search)
                )
                ||
                str_contains(
                    strtolower($product['description']),
                    strtolower($search)
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Category
        |--------------------------------------------------------------------------
        */

        $category = $request->query('category', '');

        if ($category !== '' && $category !== 'Semua') {

            $products = $products->filter(function ($product) use ($category) {

                return strtolower($product['category'])
                    === strtolower($category);

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->query('sort', 'default');

        if ($sort === 'price_low') {

            $products = $products->sortBy('price');

        } elseif ($sort === 'price_high') {

            $products = $products->sortByDesc('price');

        } elseif ($sort === 'name_asc') {

            $products = $products->sortBy('name');

        }


        /*
        |--------------------------------------------------------------------------
        | Reset Index
        |--------------------------------------------------------------------------
        */

        $products = $products->values();


        /*
        |--------------------------------------------------------------------------
        | Category List
        |--------------------------------------------------------------------------
        */

        $categories = collect($this->products())
            ->pluck('category')
            ->unique()
            ->values();


        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'selectedSort' => $sort,
        ]);
    }


    /**
     * Menampilkan detail produk.
     */
    public function show(string $slug)
    {
        $product = collect($this->products())
            ->firstWhere('slug', $slug);


        if (!$product) {
            abort(404);
        }


        return view('products.show', [
            'product' => $product,
        ]);
    }
}