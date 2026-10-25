<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStockReservation;
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    private const CART_KEY = 'bakso_pesona_cart';

    private const CHECKOUT_TOKEN_KEY = 'bakso_pesona_checkout_token';

    private const CHECKOUT_SIGNATURE_KEY = 'bakso_pesona_checkout_signature';

    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get(self::CART_KEY, []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu masih kosong.');
        }

        $products = Product::whereIn('id', array_map('intval', array_keys($cart)))->get()->keyBy('id');
        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (! $product) {
                return redirect()->route('cart.index')->with('error', 'Produk di keranjang tidak lagi tersedia.');
            }

            if (! $product->is_available) {
                return redirect()->route('cart.index')->with('error', 'Ada produk yang tidak tersedia. Hapus produk tersebut sebelum checkout.');
            }

            if (! is_numeric($quantity) || (int) $quantity < 1 || (int) $quantity > 99 || (int) $quantity > (int) $product->stock) {
                return redirect()->route('cart.index')->with('error', 'Ada produk dengan stok yang tidak mencukupi. Perbarui jumlah di keranjang sebelum checkout.');
            }

            try {
                $unitPrice = Order::rupiahToInt($product->price);
            } catch (InvalidArgumentException) {
                return redirect()->route('cart.index')->with('error', 'Harga salah satu produk belum berupa Rupiah bulat. Hubungi admin sebelum melanjutkan.');
            }

            $quantity = (int) $quantity;
            $lineTotal = $unitPrice * $quantity;
            $subtotal += $lineTotal;
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $lineTotal,
            ];
        }

        if ($items === []) {
            return redirect()->route('cart.index')->with('error', 'Produk di keranjang tidak lagi tersedia.');
        }

        $signature = hash('sha256', json_encode($cart, JSON_THROW_ON_ERROR));
        $token = $request->session()->get(self::CHECKOUT_TOKEN_KEY);

        if ($request->session()->get(self::CHECKOUT_SIGNATURE_KEY) !== $signature || ! is_string($token)) {
            $token = (string) Str::uuid();
            $request->session()->put([
                self::CHECKOUT_TOKEN_KEY => $token,
                self::CHECKOUT_SIGNATURE_KEY => $signature,
            ]);
        }

        return view('orders.create', [
            'items' => collect($items),
            'subtotal' => $subtotal,
            'checkoutToken' => $token,
            'provinces' => config('shipping.provinces', []),
            'javaProvinces' => config('shipping.java_provinces', []),
            'shippingRates' => config('shipping.rates', ['java' => 3000, 'outside_java' => 5000]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'checkout_token' => ['required', 'uuid'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9][0-9\s().-]{7,29}$/'],
            'shipping_province' => ['required', 'string', 'max:100', 'in:'.implode(',', config('shipping.provinces', []))],
            'shipping_city' => ['required', 'string', 'max:150'],
            'shipping_postal_code' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'shipping_address' => ['required', 'string', 'max:2000'],
            'delivery_landmark' => ['nullable', 'string', 'max:1000'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'checkout_token.required' => 'Token checkout tidak valid. Muat ulang halaman checkout.',
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Format nomor HP/WhatsApp tidak valid.',
            'shipping_address.required' => 'Alamat lengkap wajib diisi.',
            'shipping_province.required' => 'Provinsi wajib dipilih.',
            'shipping_province.in' => 'Provinsi tujuan tidak valid. Pilih dari daftar yang tersedia.',
            'shipping_city.required' => 'Kota/kabupaten wajib diisi.',
            'shipping_postal_code.required' => 'Kode pos wajib diisi.',
            'shipping_postal_code.regex' => 'Kode pos harus terdiri dari 5 angka.',
        ]);

        $customer = $request->user('customer');
        $existingOrder = Order::query()
            ->where('user_id', $customer->id)
            ->where('checkout_token', $validated['checkout_token'])
            ->first();

        if ($existingOrder) {
            return redirect()->route('checkout.success', $existingOrder);
        }

        if ($request->session()->get(self::CHECKOUT_TOKEN_KEY) !== $validated['checkout_token']) {
            throw ValidationException::withMessages([
                'checkout_token' => 'Token checkout sudah berubah. Muat ulang halaman checkout dan periksa keranjang.',
            ]);
        }

        $cart = $request->session()->get(self::CART_KEY, []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong.');
        }

        $order = DB::transaction(function () use ($cart, $validated, $customer) {
            User::query()
                ->whereKey($customer->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingOrder = Order::query()
                ->where('user_id', $customer->id)
                ->where('checkout_token', $validated['checkout_token'])
                ->first();

            if ($existingOrder) {
                return $existingOrder;
            }

            $products = Product::whereIn('id', array_map('intval', array_keys($cart)))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'cart' => 'Ada produk yang sudah tidak tersedia. Periksa kembali keranjang kamu.',
                ]);
            }

            foreach ($cart as $productId => $quantity) {
                $product = $products->get((int) $productId);
                if (
                    ! $product
                    || ! $product->is_available
                    || ! is_numeric($quantity)
                    || (int) $quantity < 1
                    || (int) $quantity > 99
                    || (int) $quantity > (int) $product->stock
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'Ada produk yang tidak tersedia, stoknya tidak mencukupi, atau jumlahnya tidak valid. Periksa keranjang kamu.',
                    ]);
                }
            }

            $subtotal = 0;
            foreach ($cart as $productId => $quantity) {
                try {
                    $unitPrice = Order::rupiahToInt($products->get((int) $productId)->price);
                } catch (InvalidArgumentException) {
                    throw ValidationException::withMessages([
                        'cart' => 'Harga salah satu produk belum berupa Rupiah bulat. Hubungi admin sebelum melanjutkan.',
                    ]);
                }

                $subtotal += $unitPrice * (int) $quantity;
            }

            $order = Order::create([
                'order_number' => 'BP-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(4))),
                'user_id' => $customer->id,
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'delivery_landmark' => $validated['delivery_landmark'] ?? null,
                'customer_note' => $validated['customer_note'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'shipping_cost' => in_array($validated['shipping_province'], config('shipping.java_provinces', []), true)
                    ? config('shipping.rates.java', 3000)
                    : config('shipping.rates.outside_java', 5000),
                'total_amount' => $subtotal - 0 + (in_array($validated['shipping_province'], config('shipping.java_provinces', []), true)
                    ? config('shipping.rates.java', 3000)
                    : config('shipping.rates.outside_java', 5000)),
                'status' => Order::STATUS_PENDING_CONFIRMATION,
                'payment_method' => 'qris',
                'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION,
                'checkout_token' => $validated['checkout_token'],
            ]);

            $order->recordEvent(
                'checkout_created',
                $customer,
                newValues: ['status' => $order->status, 'subtotal' => $subtotal, 'shipping_cost' => $order->shipping_cost, 'total_amount' => $order->total_amount, 'shipping_province' => $order->shipping_province],
            );

            foreach ($cart as $productId => $quantity) {
                $product = $products->get((int) $productId);
                $quantity = (int) $quantity;
                $unitPrice = Order::rupiahToInt($product->price);
                $stockBefore = (int) $product->stock;

                $item = $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $unitPrice * $quantity,
                ]);

                $product->decrement('stock', $quantity);

                OrderStockReservation::create([
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'state' => 'reserved',
                    'reserved_at' => now(),
                ]);

                ProductStockMovement::create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'actor_user_id' => $customer->id,
                    'movement_type' => 'checkout_reserved',
                    'direction' => -1,
                    'quantity' => $quantity,
                    'metadata' => ['stock_before' => $stockBefore, 'stock_after' => $stockBefore - $quantity],
                ]);

                $order->recordEvent(
                    'stock_reserved',
                    $customer,
                    metadata: ['product_id' => $product->id, 'quantity' => $quantity],
                );
            }

            return $order;
        }, 3);

        $request->session()->forget([
            self::CART_KEY,
            self::CHECKOUT_TOKEN_KEY,
            self::CHECKOUT_SIGNATURE_KEY,
        ]);

        return redirect()->route('checkout.success', $order)->with(
            'success',
            'Pesanan berhasil dibuat. Ongkir dan total akhir sudah dihitung otomatis. Tunggu instruksi pembayaran QRIS resmi dari admin.'
        );
    }

    public function success(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user('customer')->id, 404);

        $order->load('items');

        return view('orders.success', compact('order'));
    }
}
