<?php

use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::controller(ProductsController::class)->prefix('products')->group(function () {
    Route::get('/low-stock', 'lowStock')->name('products.low.stock');

    Route::post('/{product}/reserve', 'reserve')->where('product', '[0-9]+')->name('products.reserve');
    Route::post('/{product}/release', 'release')->where('product', '[0-9]+')->name('products.release');
    Route::get('/{product}/stock', 'stock')->where('product', '[0-9]+')->name('products.stock');
});

Route::apiResource('products', ProductsController::class);
Route::apiResource('customers', CustomersController::class);
