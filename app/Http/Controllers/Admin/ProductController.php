<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar katalog produk untuk admin.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'low_stock' => ['sometimes', 'boolean'],
        ]);

        $query = Product::with('category')->withCount('orderItems');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'available') {
                $query->where('is_available', true);
            } elseif ($request->input('status') === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        if ($request->boolean('low_stock')) {
            $query->where('is_available', true)->where('stock', '<=', 5);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Tampilkan formulir penambahan produk baru.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'integer', 'min:0', 'max:9999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'description' => ['nullable', 'string', 'max:2000'],
            'badge' => ['nullable', 'string', 'max:50'],
            'is_available' => ['required', 'boolean'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'stock.required' => 'Stok produk wajib diisi.',
            'image_file.image' => 'File harus berupa gambar valid.',
            'image_file.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $imagePath = null;
        $uploadedImagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'prod_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('images/products');
            if (! File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            $file->move($destinationPath, $filename);
            $imagePath = 'images/products/'.$filename;
            $uploadedImagePath = public_path($imagePath);
        } elseif (! empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        $actor = $request->user('admin');
        abort_unless($actor instanceof User, 403);
        $stock = (int) $validated['stock'];

        try {
            DB::transaction(function () use ($validated, $slug, $imagePath, $actor, $stock): void {
                $product = Product::create([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'stock' => $stock,
                    'badge' => $validated['badge'] ?? null,
                    'is_available' => $validated['is_available'],
                    'image' => $imagePath,
                ]);

                if ($stock > 0) {
                    ProductStockMovement::create([
                        'product_id' => $product->id,
                        'actor_user_id' => $actor->id,
                        'movement_type' => 'initial_stock',
                        'direction' => 1,
                        'quantity' => $stock,
                        'metadata' => [
                            'stock_before' => 0,
                            'stock_after' => $stock,
                            'reason' => 'Stok awal saat produk dibuat.',
                            'product_name' => $product->name,
                            'product_slug' => $product->slug,
                        ],
                    ]);
                }
            });
        } catch (Throwable $exception) {
            if ($uploadedImagePath && File::exists($uploadedImagePath)) {
                File::delete($uploadedImagePath);
            }

            throw $exception;
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$validated['name']}' berhasil ditambahkan ke menu.");
    }

    /**
     * Tampilkan formulir edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Perbarui data produk di database.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'integer', 'min:0', 'max:9999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'stock_change_reason' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'badge' => ['nullable', 'string', 'max:50'],
            'is_available' => ['required', 'boolean'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ]);

        if ((int) $product->stock !== (int) $validated['stock'] && blank($validated['stock_change_reason'] ?? null)) {
            throw ValidationException::withMessages([
                'stock_change_reason' => 'Alasan perubahan stok wajib diisi agar mutasi dapat diaudit.',
            ]);
        }

        $imagePath = $product->image;
        $uploadedImagePath = null;
        $oldImagePath = null;

        if ($request->hasFile('image_file')) {
            // Hapus file lokal lama jika bukan URL eksternal
            if ($product->image && ! filter_var($product->image, FILTER_VALIDATE_URL)) {
                $oldImagePath = public_path($product->image);
            }

            $file = $request->file('image_file');
            $filename = 'prod_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('images/products');
            if (! File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            $file->move($destinationPath, $filename);
            $imagePath = 'images/products/'.$filename;
            $uploadedImagePath = public_path($imagePath);
        } elseif (! empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        $actor = $request->user('admin');
        abort_unless($actor instanceof User, 403);
        try {
            $updatedProduct = DB::transaction(function () use ($product, $validated, $imagePath, $actor): Product {
                $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);
                $stockBefore = (int) $lockedProduct->stock;
                $stockAfter = (int) $validated['stock'];

                if ($stockBefore !== $stockAfter && blank($validated['stock_change_reason'] ?? null)) {
                    throw ValidationException::withMessages([
                        'stock_change_reason' => 'Alasan perubahan stok wajib diisi agar mutasi dapat diaudit.',
                    ]);
                }

                $slug = $lockedProduct->slug;
                if ($lockedProduct->name !== $validated['name']) {
                    $baseSlug = Str::slug($validated['name']);
                    $slug = $baseSlug;
                    $counter = 1;
                    while (Product::where('slug', $slug)->where('id', '!=', $lockedProduct->id)->exists()) {
                        $slug = "{$baseSlug}-{$counter}";
                        $counter++;
                    }
                }

                $lockedProduct->category_id = $validated['category_id'];
                $lockedProduct->name = $validated['name'];
                $lockedProduct->slug = $slug;
                $lockedProduct->description = $validated['description'] ?? null;
                $lockedProduct->price = $validated['price'];
                $lockedProduct->stock = $stockAfter;
                $lockedProduct->badge = $validated['badge'] ?? null;
                $lockedProduct->is_available = $validated['is_available'];
                $lockedProduct->image = $imagePath;
                $lockedProduct->save();

                if ($stockBefore !== $stockAfter) {
                    ProductStockMovement::create([
                        'product_id' => $lockedProduct->id,
                        'actor_user_id' => $actor->id,
                        'movement_type' => 'admin_adjustment',
                        'direction' => $stockAfter > $stockBefore ? 1 : -1,
                        'quantity' => abs($stockAfter - $stockBefore),
                        'metadata' => [
                            'stock_before' => $stockBefore,
                            'stock_after' => $stockAfter,
                            'reason' => $validated['stock_change_reason'],
                            'product_name' => $lockedProduct->name,
                            'product_slug' => $lockedProduct->slug,
                        ],
                    ]);
                }

                return $lockedProduct;
            }, 3);
        } catch (Throwable $exception) {
            if ($uploadedImagePath && File::exists($uploadedImagePath)) {
                File::delete($uploadedImagePath);
            }

            throw $exception;
        }

        if ($oldImagePath && File::exists($oldImagePath)) {
            File::delete($oldImagePath);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Data produk '{$updatedProduct->name}' berhasil diperbarui.");
    }

    /**
     * Hapus produk dengan aturan keamanan data transaksi.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Jika produk sudah pernah dipesan pelanggan, jangan hapus fisik (soft disable)
        if ($product->orderItems()->exists()) {
            $product->update(['is_available' => false]);

            return redirect()->route('admin.products.index')
                ->with('warning', "Produk '{$product->name}' tercatat dalam histori pesanan pelanggan. Produk tidak dapat dihapus permanen, namun statusnya telah diubah menjadi Nonaktif.");
        }

        if ($product->stock > 0) {
            $product->update(['is_available' => false]);

            return redirect()->route('admin.products.index')
                ->with('warning', "Produk '{$product->name}' memiliki stok tersisa. Produk dinonaktifkan; catat penyesuaian stok sebelum menghapusnya.");
        }

        // Hapus file gambar jika lokal
        if ($product->image && ! filter_var($product->image, FILTER_VALIDATE_URL)) {
            $oldPath = public_path($product->image);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$productName}' berhasil dihapus secara permanen.");
    }
}
