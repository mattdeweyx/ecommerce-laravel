<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Storage;






// Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/adminLogin', [AuthController::class, 'adminLogin']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'show']); // user endpoint instead of profile
    Route::put('/user', [AuthController::class, 'updateUserInfo']);
});

// User Management (Requires admin role)
Route::middleware(['auth:sanctum', 'CheckTokenableType:App\Models\Admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{product}', [ProductController::class, "destroy"]);
    // Admin show or add or remove admins or users
    Route::delete('/users/{ID}', [AuthController::class, "destroyAdmin"]);
    Route::delete('/admins/{ID}', [AuthController::class, "destroyUser"]);
    Route::get('/user', [AuthController::class, 'index']);
    Route::get('/admin', [AuthController::class, 'index']);
    Route::post('/users/admin', [AuthController::class, 'Adminsignup']);
    Route::post('/users/user', [AuthController::class, 'userSignup']);
});

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/categories', [ProductController::class, 'indexCategory']);
Route::get('/products/categories/{category}', [ProductController::class, 'showCategory']);
Route::get('/products/popular/{numberOfProducts}', [ProductController::class, 'showPopular']);
Route::get('/products/newest/{numberOfProducts}', [ProductController::class, 'showNewest']);
Route::post('/products/search', [ProductController::class, 'search']);
Route::get('/products/homeApp/{homePopularNumber}/{homeNewestNumber}/{popularNumber}/{menNumber}/{womenNumber}/{favoriteNumber}', [ProductController::class, 'showHomeApp']);

// Favorites (Requires user role)
Route::middleware(['auth:sanctum', 'CheckTokenableType:App\Models\User'])->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/products/{product}/favorite', [FavoriteController::class, 'addToFavorites']);
    Route::delete('/products/{product}/unfavorite', [FavoriteController::class, 'removeFromFavorites']);
    Route::post('/products/{product}/rate', [ProductController::class, 'rate']);
    Route::delete('/products/{product}/unrate', [ProductController::class, 'unrate']);

    Route::post('/cart/{id}/decrease', [CartController::class, 'decrease']);
    Route::post('/cart/{id}/increase', [CartController::class, 'increase']);
    Route::delete('/cart/{id}/remove', [CartController::class, 'destroy']);
    Route::delete('/cartClear', [CartController::class, 'destroy']);
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::get('/cartCount', [CartController::class, 'getCartCount']);
    Route::post('/cart', [CartController::class, 'store']);
    

    Route::get('/order', [OrderController::class, 'index']);
    Route::post('/order', [OrderController::class, 'store']);

});


// Orders (Only store for creating new orders)
Route::apiResource('orders', OrderController::class)->only(['store']);

