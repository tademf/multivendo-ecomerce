<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\SettingsController;

// ========== PUBLIC ROUTES ==========
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/category/{categorySlug}', [HomeController::class, 'categoryProducts'])->name('category.products');

// Product routes (public)
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/public-products', [ProductController::class, 'publicIndex'])->name('public.products');

// Auth routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// ========== PUBLIC API ROUTES ==========
Route::get('/api/categories', function () {
    return \App\Models\Category::withCount('products')->get();
})->name('api.categories');

// ========== PROTECTED ROUTES ==========
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
    
    // Products Management
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/my-products/{product}', [ProductController::class, 'showMyProduct'])->name('products.show.my');
    });
    
    // Wishlist Routes
    // Route::prefix('wishlist')->group(function () {
    //     Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    //     Route::post('/add', [WishlistController::class, 'store'])->name('wishlist.add');
    //     Route::post('/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    //     Route::delete('/{product}', [WishlistController::class, 'destroy'])->name('wishlist.remove');
    //     Route::delete('/', [WishlistController::class, 'clear'])->name('wishlist.clear');
    // });
    
    // Cart Routes
    // Route::prefix('cart')->group(function () {
    //     Route::get('/', [CartController::class, 'index'])->name('cart');
    //     Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    //     Route::post('/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
    //     Route::post('/update/{item}', [CartController::class, 'update'])->name('cart.update');
    //     Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    //     Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
    // });
    
    // Orders Routes
    Route::prefix('orders')->group(function () {
        Route::get('/my-orders', [OrderController::class, 'customerOrders'])->name('orders.customer');
        Route::get('/manage-orders', [OrderController::class, 'vendorOrders'])->name('orders.vendor');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });
    
    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::get('/', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/process', [PaymentController::class, 'process'])->name('payment.process');
        Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::post('/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
    });
    
   Route::middleware(['auth'])->group(function () {
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.page');
        Route::post('/verify-password', [SettingsController::class, 'verifyPassword'])->name('settings.verify-password');
        
        // Change this to POST for file uploads
        Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        
        Route::put('/account-number', [SettingsController::class, 'updateAccountNumber'])->name('settings.account-number.update');
        Route::put('/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
        Route::post('/profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('settings.profile-picture.update');
        Route::delete('/account', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
    });
});
    
    // Verification Routes
    Route::prefix('verification')->group(function () {
        Route::get('/request', [VerificationController::class, 'create'])->name('verification.request');
        Route::post('/submit', [VerificationController::class, 'store'])->name('verification.submit');
        Route::put('/update-account-number', [VerificationController::class, 'updateAccountNumber'])->name('verification.update-account-number');
        Route::post('/upload-id-image', [VerificationController::class, 'uploadIdImage'])->name('verification.upload-id-image');
    });
    
    // PROTECTED API ROUTES
    Route::prefix('api')->group(function () {
        Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('api.wishlist.count');
        Route::get('/cart/count', [CartController::class, 'count'])->name('api.cart.count');
    });
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ========== FALLBACK ==========
Route::fallback(function () {
    return redirect('/');
});