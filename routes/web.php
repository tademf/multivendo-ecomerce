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
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdditionalImageController;

// ========== PUBLIC ROUTES ==========
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/category/{categorySlug}', [HomeController::class, 'categoryProducts'])->name('category.products');

// Product Details Route - Public (for customers)
Route::get('/product/{id}', [ProductController::class, 'showDetails'])->name('product.details');

// Product routes (public)
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/public-products', [ProductController::class, 'publicIndex'])->name('public.products');

// ========== AUTH ROUTES ==========

// Guest routes (accessible only when NOT logged in)
Route::middleware(['guest'])->group(function () {
    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ========== AUTHENTICATED ROUTES ==========
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Check session
    Route::get('/check-session', [AuthController::class, 'checkSession']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    // Update profile picture to accept both PUT and POST
    Route::match(['put', 'post'], '/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
    
    // Products Management
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        
        // Accept both PUT and POST for update
        Route::match(['put', 'post'], '/{product}', [ProductController::class, 'update'])->name('products.update');
        
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/my-products/{product}', [ProductController::class, 'showMyProduct'])->name('products.show.my');
        
        // NEW ROUTE: Additional Images Page
        Route::get('/{product}/add-images', [ProductController::class, 'showAddImages'])->name('products.add.images');
    });

    // Discount Management Routes
    Route::prefix('discounts')->group(function () {
        Route::get('/', [DiscountController::class, 'index'])->name('discounts.index');
        Route::post('/', [DiscountController::class, 'store'])->name('discounts.store');
        
        // Accept both PUT and POST for update
        Route::match(['put', 'post'], '/{id}', [DiscountController::class, 'update'])->name('discounts.update');
        
        Route::delete('/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
    });
    
    // ========== CART ROUTES ==========
    Route::prefix('cart')->group(function () {
        // Display cart page
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        
        // Add item to cart
        Route::post('/', [CartController::class, 'store'])->name('cart.store');
        
        // Update cart item quantity
        Route::match(['put', 'post'], '/{id}', [CartController::class, 'update'])->name('cart.update');
        
        // Remove item from cart
        Route::delete('/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
        
        // Clear entire cart
        Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
        
        // Old routes for compatibility
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
        Route::match(['post', 'put'], '/update/{item}', [CartController::class, 'update'])->name('cart.update-old');
        Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    });
    
    // ========== WISHLIST ROUTES ==========
    Route::prefix('wishlist')->group(function () {
        // Display wishlist page
        Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
        
        // Add item to wishlist
        Route::post('/', [WishlistController::class, 'store'])->name('wishlist.store');
        
        // Remove item from wishlist
        Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
        
        // Move item from wishlist to cart
        Route::post('/{id}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
        
        // Old routes for compatibility
        Route::post('/add', [WishlistController::class, 'add'])->name('wishlist.add');
        Route::post('/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        Route::delete('/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
    });
    
    // Order Routes
    Route::prefix('orders')->group(function () {
        Route::get('/my-orders', [ShipmentController::class, 'customerOrders'])->name('orders.customer');
        Route::get('/manage-orders', [ShipmentController::class, 'vendorOrders'])->name('orders.vendor');
        Route::get('/{order}', [ShipmentController::class, 'show'])->name('orders.show');
        Route::post('/{order}/cancel', [ShipmentController::class, 'cancel'])->name('orders.cancel');
        
        // Accept both PUT and POST for status update
        Route::match(['put', 'post'], '/{order}/status', [ShipmentController::class, 'updateStatus'])->name('orders.update-status');
    });
    
    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::get('/', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/process', [PaymentController::class, 'process'])->name('payment.process');
        Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::post('/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
    });

    // Message Routes
    Route::prefix('messages')->group(function () {
        Route::get('/shipment/{shipment}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/shipment/{shipment}/send', [MessageController::class, 'sendMessage'])->name('messages.send');
        Route::get('/conversations', [MessageController::class, 'conversations'])->name('messages.conversations');
        Route::get('/shipment/{shipment}/list', [MessageController::class, 'getMessages'])->name('messages.list');
        Route::get('/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    });
    
    // Settings Routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.page');
        Route::post('/verify-password', [SettingsController::class, 'verifyPassword'])->name('settings.verify-password');
        
        // Accept both PUT and POST for profile update
        Route::match(['put', 'post'], '/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        
        // Accept both PUT and POST for account number update
        Route::match(['put', 'post'], '/account-number', [SettingsController::class, 'updateAccountNumber'])->name('settings.account-number.update');
        
        // Accept both PUT and POST for password update
        Route::match(['put', 'post'], '/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
        
        // Accept both PUT and POST for profile picture update
        Route::match(['put', 'post'], '/profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('settings.profile-picture.update');
        
        Route::delete('/account', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
    });
    
    // Verification Routes
    Route::prefix('verification')->group(function () {
        Route::get('/request', [VerificationController::class, 'create'])->name('verification.request');
        Route::post('/submit', [VerificationController::class, 'store'])->name('verification.submit');
        Route::post('/upload-id-image', [VerificationController::class, 'uploadIdImage'])->name('verification.upload-id-image');
    });
    
    // PROTECTED API ROUTES
    Route::prefix('api')->group(function () {
        // Additional Images API routes
        Route::post('/product/{id}/additional-images', [AdditionalImageController::class, 'store'])->name('api.product.additional.images.store');
        Route::get('/product/{id}/additional-images', [AdditionalImageController::class, 'index'])->name('api.product.additional.images');
        Route::delete('/additional-images/{id}', [AdditionalImageController::class, 'destroy'])->name('api.additional.images.destroy');
        Route::post('/product/{id}/additional-images/order', [AdditionalImageController::class, 'updateOrder'])->name('api.product.additional.images.order');
        Route::post('/additional-images/{id}/select', [AdditionalImageController::class, 'setSelected'])->name('api.additional.images.select');
        
        // Wishlist API
        Route::get('/wishlist/count', [WishlistController::class, 'getWishlistCount'])->name('api.wishlist.count');
        
        // Cart API
        Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('api.cart.count');
        
        // Discount API routes
        Route::get('/discounts', [DiscountController::class, 'apiIndex'])->name('api.discounts.index');
        Route::get('/discounts/{id}', [DiscountController::class, 'apiShow'])->name('api.discounts.show');
        Route::post('/discounts', [DiscountController::class, 'apiStore'])->name('api.discounts.store');
        
        // Accept both PUT and POST for API discount update
        Route::match(['put', 'post'], '/discounts/{id}', [DiscountController::class, 'apiUpdate'])->name('api.discounts.update');
        
        Route::delete('/discounts/{id}', [DiscountController::class, 'apiDestroy'])->name('api.discounts.destroy');
        Route::get('/discounts/product/{productId}/active', [DiscountController::class, 'apiActiveProductDiscounts'])->name('api.discounts.product.active');
    });
});

// ========== PUBLIC API ROUTES ==========
Route::get('/api/categories', function () {
    return \App\Models\Category::withCount('products')->get();
})->name('api.categories');

// ========== FALLBACK ==========
Route::fallback(function () {
    return redirect('/');
});