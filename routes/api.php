<?php

use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\CartController;

Route::get('/menu-items', [MenuItemController::class, 'index']);
Route::get('/user', function () {
    return response()->json([
        'logged_in' => auth()->check(),
    ]);
});

// Cart routes with named routes added
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'showCart'])->name('cart.show');
    Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/{cartItemId}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/{cartItemId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
});


use App\Http\Controllers\Api\OrderController;

Route::middleware(['web', 'auth'])->group(function () {
    // Create a new order
    Route::post('/orders', [OrderController::class, 'createOrder']);

    // Show the details of a specific order
    Route::get('/orders/{orderId}', [OrderController::class, 'showOrderDetails']);

    // Update the status of an order (e.g., after payment or manual update)
    // Update the status of an order
Route::patch('/orders/{orderId}', [OrderController::class, 'updateOrderStatus'])->name('orders.update');
Route::post('/checkout', [CheckoutController::class, 'initiateCheckout'])->name('checkout.initiate');
Route::post('/checkout/verify', [CheckoutController::class, 'verifyPayment'])->name('checkout.verify');

});
use App\Http\Controllers\Api\CheckoutController;
