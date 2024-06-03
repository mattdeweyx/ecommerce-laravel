<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    Route::post('/products/{id}/rate', [ProductController::class, 'rate']);
    Route::delete('/products/{id}/unrate', [ProductController::class, 'unrate']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/products/{id}/favorite', [FavoriteController::class, 'addToFavorites']);
    Route::delete('/products/{id}/unfavorite', [FavoriteController::class, 'removeFromFavorites']);
});

Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('cart', CartController::class)->except(['index', 'store']);
Route::post('/cart', [CartController::class, 'store']);
Route::apiResource('orders', OrderController::class)->only(['store']);

Route::post('/signup', [RegisteredUserController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

require __DIR__.'/auth.php';

