<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSetController;
use Illuminate\Support\Facades\Route;

Route::get('product-sets', [ProductSetController::class, 'index']);
Route::middleware('api.key')->group(function () {
    Route::post('product-sets', [ProductSetController::class, 'store']);
    Route::put('product-sets/{id}', [ProductSetController::class, 'update']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
});
