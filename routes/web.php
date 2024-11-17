<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::get('/menu', function () {
    return view('menu'); // Create a menu view
})->name('menu');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes for orders and cart for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'showCart'])->name('cart');
});
// Protected routes for orders and cart for authenticated users
Route::middleware(['auth'])->group(function () {
    // Show orders page for the authenticated user
    Route::get('/orders', [OrderController::class, 'showOrdersView'])->name('orders');

    // Show thgie details of a specific order (order details page)
    Route::get('/orders/{orderId}', [OrderController::class, 'showOrderDetails'])->name('orders.show');
    
});
// Middleware for role-based access
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Customer-specific routes
    Route::get('/customer/dashboard', function () {
        return view('customer.dashboard'); // Customer dashboard view
    })->name('customer.dashboard');
});

Route::middleware(['auth', 'role:staff'])->group(function () {
    // Staff-specific routes
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    
});

// Route for password reset (if enabled)
Route::get('/password/reset', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [LoginController::class, 'reset'])->name('password.update');
// In web.php


Route::middleware(['auth'])->get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout');


// Define the route for processing the checkout form submission
