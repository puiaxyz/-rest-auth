<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminDashboardController;

// -------------------------------------
// Public Routes
// -------------------------------------

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::get('/menu', fn() => view('menu'))->name('menu');

// -------------------------------------
// Authentication Routes
// -------------------------------------

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset (Optional)
Route::get('/password/reset', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [LoginController::class, 'reset'])->name('password.update');

// -------------------------------------
// Protected Routes (Authenticated Users)
// -------------------------------------

// Cart
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'showCart'])->name('cart');

    // Orders
    Route::get('/orders', [OrderController::class, 'showOrdersView'])->name('orders');
    Route::get('/orders/{orderId}', [OrderController::class, 'showOrderDetails'])->name('orders.show');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout');
});

// -------------------------------------
// Role-Based Routes
// -------------------------------------

// Customer Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', fn() => view('customer.dashboard'))->name('customer.dashboard');
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::patch('/staff/orders/{orderId}/update', [StaffDashboardController::class, 'updateOrderStatus'])->name('staff.orders.update');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Additional Admin Pages
    Route::get('/user-management', [AdminDashboardController::class, 'userManagement'])->name('userManagement');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
});
