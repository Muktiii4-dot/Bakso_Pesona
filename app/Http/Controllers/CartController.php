<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Key session untuk menyimpan data keranjang.
     */
    private const CART_KEY = 'bakso_pesona_cart';

    /**
     * Menampilkan isi keranjang belanja.
     */
    public function index()
    {
        $cart = session()->get(self::CART_KEY, []);

        // Ambil data produk yang tersimpan di keranjang.
        $products = Product::whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        // Gabungkan data produk, kuantitas, dan subtotal.
        $items = collect($cart)
            ->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);

                // Abaikan produk yang sudah tidak ditemukan di database.
                if (!$product) {
                    return null;
                }

                $quantity = (int) $quantity;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();

        // Hitung total seluruh produk.
        $total = $items->sum('subtotal');

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Menambahkan produk ke keranjang.
     */
    public function store(Request $request, Product $product)
    {
        if (!$product->is_available) {
            return back()->with(
                'error',
                'Maaf, produk ini sedang tidak tersedia.'
            );
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session()->get(self::CART_KEY, []);

        $productId = (string) $product->id;
        $currentQuantity = (int) ($cart[$productId] ?? 0);
        $newQuantity = $currentQuantity + (int) $validated['quantity'];

        if ($newQuantity > 99) {
            return back()->with(
                'error',
                'Maksimal 99 unit untuk setiap produk.'
            );
        }

        $cart[$productId] = $newQuantity;

        session()->put(self::CART_KEY, $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Memperbarui kuantitas produk di keranjang.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if (!$product->is_available) {
            return back()->with(
                'error',
                'Produk ini sedang tidak tersedia.'
            );
        }

        $cart = session()->get(self::CART_KEY, []);
        $productId = (string) $product->id;

        if (!array_key_exists($productId, $cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        $cart[$productId] = (int) $validated['quantity'];

        session()->put(self::CART_KEY, $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari keranjang.
     */
    public function destroy(Product $product)
    {
        $cart = session()->get(self::CART_KEY, []);
        $productId = (string) $product->id;

        unset($cart[$productId]);

        session()->put(self::CART_KEY, $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}
