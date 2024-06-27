<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('auth/logout', [AuthController::class, 'logout']);

Route::apiResource('products', ProductController::class)->only([
    'index',
    'store',
]);

Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::delete('/cart/remove/{item}', [CartController::class, 'removeFromCart']);
Route::get('/cart', [CartController::class, 'viewCart']);
