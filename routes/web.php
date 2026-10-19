<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('home');
})->name('home');


/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::get('/produk', [
    ProductController::class,
    'index'
])->name('products.index');


Route::get('/produk/{slug}', [
    ProductController::class,
    'show'
])->name('products.show');
