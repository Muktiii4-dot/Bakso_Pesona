<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CartController extends Controller
{
    private const CART_KEY = 'bakso_pesona_cart';

    public function index()
    {
        $items = $this->cartItems();
        $total = $items->sum('subtotal');

        return view('cart.index', compact('items', 'total'));
    }

    public function store(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        if (! $product->is_available || $product->stock < 1) {
            return $this->cartError($request, 'Maaf, produk ini sedang tidak tersedia.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $request->session()->get(self::CART_KEY, []);
        $productId = (string) $product->id;
        $newQuantity = (int) ($cart[$productId] ?? 0) + (int) $validated['quantity'];
        $maximum = min(99, (int) $product->stock);

        if ($newQuantity > $maximum) {
            return $this->cartError(
                $request,
                "Stok tersedia untuk produk ini hanya {$maximum} unit."
            );
        }

        $cart[$productId] = $newQuantity;
        $request->session()->put(self::CART_KEY, $cart);
        $this->forgetCheckoutToken($request);

        if ($request->expectsJson()) {
            return response()->json($this->cartSnapshot());
        }

        return redirect()->route('home')->with(
            'cart_success',
            'Produk berhasil dimasukkan ke keranjang.'
        );
    }

    public function update(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if (! $product->is_available || $product->stock < 1) {
            return $this->cartError($request, 'Produk ini sedang tidak tersedia. Hapus dari keranjang untuk melanjutkan.');
        }

        $cart = $request->session()->get(self::CART_KEY, []);
        $productId = (string) $product->id;

        if (! array_key_exists($productId, $cart)) {
            return $this->cartError($request, 'Produk tidak ditemukan di keranjang.', 404);
        }

        $maximum = min(99, (int) $product->stock);
        if ((int) $validated['quantity'] > $maximum) {
            return $this->cartError(
                $request,
                "Stok tersedia untuk produk ini hanya {$maximum} unit."
            );
        }

        $cart[$productId] = (int) $validated['quantity'];
        $request->session()->put(self::CART_KEY, $cart);
        $this->forgetCheckoutToken($request);

        return $this->cartSuccess($request, 'Jumlah produk berhasil diperbarui.');
    }

    public function destroy(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $cart = $request->session()->get(self::CART_KEY, []);
        unset($cart[(string) $product->id]);
        $request->session()->put(self::CART_KEY, $cart);
        $this->forgetCheckoutToken($request);

        return $this->cartSuccess($request, 'Produk berhasil dihapus dari keranjang.');
    }

    private function cartItems(): Collection
    {
        $cart = session()->get(self::CART_KEY, []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)
            ->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                $quantity = (int) $quantity;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => Order::rupiahToInt($product->price) * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    private function cartSnapshot(): array
    {
        $items = $this->cartItems();

        return [
            'count' => (int) $items->sum('quantity'),
            'items_count' => $items->count(),
            'subtotal' => (int) $items->sum('subtotal'),
            'items' => $items->map(function (array $item) {
                $product = $item['product'];
                $image = $product->image;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => Order::rupiahToInt($product->price),
                    'quantity' => $item['quantity'],
                    'subtotal' => (float) $item['subtotal'],
                    'stock' => (int) $product->stock,
                    'available' => (bool) $product->is_available && $product->stock > 0,
                    'image_url' => $image
                        ? (filter_var($image, FILTER_VALIDATE_URL) ? $image : asset($image))
                        : null,
                    'product_url' => route('products.show', $product->slug),
                    'update_url' => route('cart.update', $product),
                    'delete_url' => route('cart.destroy', $product),
                ];
            })->all(),
        ];
    }

    private function cartSuccess(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json($this->cartSnapshot());
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    private function cartError(Request $request, string $message, int $status = 422): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => ['cart' => [$message]],
            ], $status);
        }

        return back()->with('error', $message);
    }

    private function forgetCheckoutToken(Request $request): void
    {
        $request->session()->forget([
            'bakso_pesona_checkout_token',
            'bakso_pesona_checkout_signature',
        ]);
    }
}
