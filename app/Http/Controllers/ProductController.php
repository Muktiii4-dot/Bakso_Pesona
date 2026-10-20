<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Mengubah model Product menjadi array agar tetap kompatibel
     * dengan Blade yang menggunakan format data sebelumnya.
     */
    private function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'category' => $product->category?->name ?? 'Lainnya',
            'description' => $product->description,
            'price' => (float) $product->price,
            'image' => $product->image,
            'badge' => $product->badge,
            'is_available' => $product->is_available,
        ];
    }

    /**
     * Menampilkan katalog produk.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->where('is_available', true);

        // Pencarian nama dan deskripsi produk.
        $search = trim($request->query('q', ''));

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter kategori berdasarkan nama kategori.
        $category = $request->query('category', '');

        if ($category !== '' && $category !== 'Semua') {
            $query->whereHas('category', function ($builder) use ($category) {
                $builder->where('name', $category);
            });
        }

        // Pengurutan produk.
        $sort = $request->query('sort', 'default');

        if ($sort === 'price_low') {
            $query->orderBy('price');
        } elseif ($sort === 'price_high') {
            $query->orderByDesc('price');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name');
        } else {
            $query->orderBy('id');
        }

        // Ambil data dari database dan pertahankan format array lama.
        $products = $query->paginate(4)->withQueryString();
        $products->getCollection()->transform(
            fn (Product $product) => $this->formatProduct($product)
        );

        // Ambil kategori langsung dari database.
        $categories = Category::query()
            ->orderBy('name')
            ->pluck('name');

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'selectedSort' => $sort,
        ]);
    }

    /**
     * Menampilkan detail satu produk berdasarkan slug.
     */
    public function show(string $slug)
    {
        $product = Product::query()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('products.show', [
            'product' => $this->formatProduct($product),
        ]);
    }
}
