<?php

namespace App\Http\Controllers;

use Ap\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private const CART_KEY = 'bakso_pesona_cart';

    /**
     * Menampilkan isi keranjang
     */

    public function index()
    {
        $cart = session()->get(Self::CART_KEY, []);

        $product = Product::whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = collect($cart)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);

            if(!$product) {
                return null;
            }

            return [
                'product' => $product,
                'quantity' => (int) $quantity,
                'subtotal' => (float) $product->price * (int) $quantity,
            ];
        })->filter()->values();

        $total = $items->sum('subtotal');
        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Menambahkan produk ke keranjang
     */

    public function store(Request $request, Product $product)
    {
        if(!$product->is_available) {
            return back()->with('error', 'Maaf, produk ini sedang tidak tersedia.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session()->get(self::CART_KEY, []);
        $productId = (string) $product->id;
        $currentQuantity = (int) ($cart[$productId] ?? 0);
        $newQuantity = $currentQuantity + (int) $validated['quantity'];

        if ($newQuantity > 99) {
            return back()->with('error', 'Maksimal 99 unit untuk setiap produk.');
        }

        $cart[$productId] = $newQuantity;

        session()->put(self::CART_KEY, $cart);

        return redirect()
            ->route('cart.index')
            ->with('succes', 'Produk berhasil ditambahkan ke dalam keranjang.');
    }

    /**
     * Memperbarui kuantitas produk
     */

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if (!$product->is_available) {
            return back()->with('error', 'Produk ini tidak tersedia.');
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
            ->with('succes', 'Jumlah produk berhasil diperbarui.');
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
            ->with('succes', 'Produk berhasil dihapus dari keranjang.');
    }
}
