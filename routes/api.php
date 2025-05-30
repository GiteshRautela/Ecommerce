<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Product Management Routes
Route::get('/products', [ProductController::class, 'index']); // List products (Public)
Route::post('/products', [ProductController::class, 'store'])->middleware('auth:sanctum', 'isAdmin'); // Create product (Admin)
Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('auth:sanctum', 'isAdmin'); // Update product (Admin)
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('auth:sanctum', 'isAdmin'); // Delete product (Admin)

// Order Management Routes
Route::post('/orders', [OrderController::class, 'store'])->middleware('auth:sanctum'); // Place order (User)
Route::get('/orders', [OrderController::class, 'index'])->middleware('auth:sanctum'); // List user orders (User)
Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->middleware('auth:sanctum', 'isAdmin'); // List all orders (Admin)
