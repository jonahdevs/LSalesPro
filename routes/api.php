<?php

use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('products', ProductsController::class);
Route::apiResource('customers', CustomersController::class);
