<?php

use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\WarehousesController;
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
Route::controller(CustomersController::class)->group(function () {
    Route::get('customers/{customer}/orders', 'orderHistory')->name('customer.orders');
    Route::get('customers/{customer}/credit-status', 'creditStatus')->name('customer.credit-status');
    Route::get('customers/map-data', 'mapData')->name('customer.map-data');
});

Route::apiResource('customers', CustomersController::class);

Route::controller(WarehousesController::class)->group(function () {
    Route::get('/warehouses', 'index');
    Route::get('/warehouses/{warehouse}/inventory', 'inventory');
});

Route::controller(StockTransferController::class)->group(function () {
    Route::post('/stock-transfers', 'transfer');
    Route::get('/stock-transfers', 'history');
});

Route::controller(OrdersController::class)->group(function () {
    Route::get('orders', 'index')->name('orders.index');
    Route::get('orders/{order}', 'show')->name('orders.show');
    Route::post('orders', 'store')->name('orders.store');
    Route::put('orders/{order}/status', 'update')->name('orders.update');
    Route::get('orders/{order}/invoice', 'invoice')->name('orders.invoice');
    Route::post('orders/calculate-total', 'calculateTotal')->name('orders.calculate-total');
});


