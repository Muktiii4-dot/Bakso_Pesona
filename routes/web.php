<?php

<<<<<<< HEAD
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
=======
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');
>>>>>>> b6e12ae2d658d726b7aeb6d4674d9599852860b2
