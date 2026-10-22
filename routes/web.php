<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredProducts = product::with('category')
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
    'show'
])->name('products.show');

/*
|--------------------------------------------------------------------------
| Keranjang Routes
|--------------------------------------------------------------------------
*/

Route::get('/keranjang', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/keranjang/tambah/{product}', [CartController::class, 'store'])
    ->name('cart.store');

Route::patch('/keranjang/{product}', [CartController::class, 'update'])
    ->name('cart.update');

Route::delete('/keranjang/{product}', [CartController::class, 'destroy'])
    ->name('cart.destroy');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
