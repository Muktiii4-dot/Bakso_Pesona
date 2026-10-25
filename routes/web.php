<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderRatingController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredProducts = Product::with('category')
        ->where('is_available', true)
        ->latest()
        ->take(4)
        ->get();

    $categories = Category::withCount([
        'products' => function ($query) {
            $query->where('is_available', true);
        },
    ])
        ->orderBy('name')
        ->get();

    return view('home', compact(
        'featuredProducts',
        'categories'
    ));
})->name('home');

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::get('/produk', [
    ProductController::class,
    'index',
])->name('products.index');

Route::get('/produk/{slug}', [
    ProductController::class,
    'show',
])->name('products.show');

/*
|--------------------------------------------------------------------------
| Keranjang Routes
|--------------------------------------------------------------------------
*/

Route::middleware('shopper')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/keranjang/tambah/{product}', [CartController::class, 'store'])
        ->name('cart.store');

    Route::patch('/keranjang/{product}', [CartController::class, 'update'])
        ->name('cart.update');

    Route::delete('/keranjang/{product}', [CartController::class, 'destroy'])
        ->name('cart.destroy');
});

/* Checkout wajib menggunakan akun pelanggan yang sudah login. */
Route::middleware(['auth:customer', 'customer'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:6,1')->name('checkout.store');
    Route::get('/pesanan/{order}/berhasil', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/akun', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/akun', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::get('/pesanan', [AccountController::class, 'orders'])->name('orders.index');
    Route::get('/pesanan/{order}', [AccountController::class, 'showOrder'])->whereNumber('order')->name('orders.show');
    Route::post('/pesanan/{order}/batalkan', [AccountController::class, 'requestCancellation'])->whereNumber('order')->name('orders.cancel');
    Route::post('/pesanan/{order}/rating', [AccountController::class, 'storeRating'])->whereNumber('order')->name('orders.rating.store');

});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1')
    ->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth:customer', 'customer'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.store');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware(['auth:admin', 'admin'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:admin', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/audit', [AuditController::class, 'index'])
            ->name('audit.index');

        // ─── Manajemen Pesanan ───────────────────────────────────────────────
        Route::get('/pesanan', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/pesanan/{order}', [OrderController::class, 'show'])
            ->whereNumber('order')
            ->name('orders.show');

        Route::put('/pesanan/{order}/konfirmasi-ongkir', [OrderController::class, 'confirmShipping'])
            ->whereNumber('order')
            ->name('orders.confirm-shipping');

        Route::put('/pesanan/{order}/instruksi-pembayaran', [OrderController::class, 'issuePaymentInstructions'])
            ->whereNumber('order')
            ->name('orders.issue-payment-instructions');

        Route::put('/pesanan/{order}/status-pembayaran', [OrderController::class, 'updatePaymentStatus'])
            ->whereNumber('order')
            ->name('orders.update-payment-status');

        Route::put('/pesanan/{order}/review-pembayaran', [OrderController::class, 'resolvePaymentReview'])
            ->whereNumber('order')
            ->name('orders.resolve-payment-review');

        Route::put('/pesanan/{order}/status', [OrderController::class, 'updateStatus'])
            ->whereNumber('order')
            ->name('orders.update-status');

        Route::put('/pesanan/{order}/pembatalan/{cancellationRequest}', [OrderController::class, 'decideCancellation'])
            ->whereNumber('order')
            ->whereNumber('cancellationRequest')
            ->name('orders.decide-cancellation');

        Route::post('/pesanan/{order}/batalkan', [OrderController::class, 'cancelByAdmin'])
            ->whereNumber('order')
            ->name('orders.cancel');

        Route::put('/pesanan/{order}/refund', [OrderController::class, 'completeRefund'])
            ->whereNumber('order')
            ->name('orders.complete-refund');

        Route::put('/pesanan/{order}/stok', [OrderController::class, 'restockCancelledOrder'])
            ->whereNumber('order')
            ->name('orders.restock-cancelled');

        Route::get('/ulasan', [OrderRatingController::class, 'index'])
            ->name('ratings.index');

        Route::put('/ulasan/{rating}', [OrderRatingController::class, 'moderate'])
            ->whereNumber('rating')
            ->name('ratings.moderate');

        // ─── Manajemen Produk (CRUD) ─────────────────────────────────────────
        Route::get('/produk', [AdminProductController::class, 'index'])
            ->name('products.index');

        Route::get('/produk/tambah', [AdminProductController::class, 'create'])
            ->name('products.create');

        Route::post('/produk', [AdminProductController::class, 'store'])
            ->name('products.store');

        Route::get('/produk/{product}/edit', [AdminProductController::class, 'edit'])
            ->whereNumber('product')
            ->name('products.edit');

        Route::put('/produk/{product}', [AdminProductController::class, 'update'])
            ->whereNumber('product')
            ->name('products.update');

        Route::delete('/produk/{product}', [AdminProductController::class, 'destroy'])
            ->whereNumber('product')
            ->name('products.destroy');

        // ─── Manajemen Kategori (CRUD) ───────────────────────────────────────
        Route::get('/kategori', [AdminCategoryController::class, 'index'])
            ->name('categories.index');

        Route::post('/kategori', [AdminCategoryController::class, 'store'])
            ->name('categories.store');

        Route::get('/kategori/{category}/edit', [AdminCategoryController::class, 'edit'])
            ->whereNumber('category')
            ->name('categories.edit');

        Route::put('/kategori/{category}', [AdminCategoryController::class, 'update'])
            ->whereNumber('category')
            ->name('categories.update');

        Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])
            ->whereNumber('category')
            ->name('categories.destroy');
    });
