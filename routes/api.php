<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/products/{product}', [ProductController::class, 'getSingleProduct']);

// Authenticated Routes 
Route::middleware(['auth:jwt'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/getuser', [AuthController::class, 'getUser']);

    // Creating order
    Route::middleware('role:user')->group(function () {
        Route::post('/orders', [OrderController::class, 'saveOrder']); // Users can place orders
    });

    // For Managing Orders and Products
    Route::middleware('role:admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'getOrders']);    
        Route::get('/orders/{order}', [OrderController::class, 'showOrder']); 
        Route::put('/orders/{order}', [OrderController::class, 'updateOrder']); 
        Route::delete('/orders/{order}', [OrderController::class, 'deleteOrder']);
        Route::post('/products', [ProductController::class, 'saveProduct']);
        Route::put('/products/{product}', [ProductController::class, 'updateProduct']);
        Route::delete('/products/{product}', [ProductController::class, 'deleteProduct']);
    
    });

});
