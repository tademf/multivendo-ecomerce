<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
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

// ========== GUEST ROUTES ==========
Route::middleware(['guest'])->group(function () {
    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::post('/forgot-password-otp', [AuthController::class, 'sendOtp'])->name('password.otp.send');
    Route::post('/verify-login-otp', [AuthController::class, 'verifyOtp'])->name('password.otp.verify');
});

// ========== AUTHENTICATED ROUTES ==========
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Check session
    Route::get('/check-session', [AuthController::class, 'checkSession']);
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::match(['put', 'post'], '/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
    
    // -----------------------------------------------------------------
    // VERIFIED ONLY ROUTES (ምርቶች፣ ቅናሾች እና ቬንደር ኦርደር)
    // -----------------------------------------------------------------
    Route::middleware([\App\Http\Middleware\EnsureUserIsVerified::class])->group(function () {
        
        // Products Management
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('products.index');
            Route::get('/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/', [ProductController::class, 'store'])->name('products.store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::match(['put', 'post'], '/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
            Route::get('/my-products/{product}', [ProductController::class, 'showMyProduct'])->name('products.show.my');
            Route::get('/{product}/add-images', [ProductController::class, 'showAddImages'])->name('products.add.images');
        });

        // Discount Management Routes
        Route::prefix('discounts')->group(function () {
            Route::get('/', [DiscountController::class, 'index'])->name('discounts.index');
            Route::post('/', [DiscountController::class, 'store'])->name('discounts.store');
            Route::match(['put', 'post'], '/{id}', [DiscountController::class, 'update'])->name('discounts.update');
            Route::delete('/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
        });

        // Manage Orders (Vendor Only)
        Route::get('/orders/manage-orders', [ShipmentController::class, 'vendorOrders'])->name('orders.vendor');

        // Protected API for Products/Discounts
        Route::prefix('api')->group(function () {
            Route::post('/product/{id}/additional-images', [AdditionalImageController::class, 'store'])->name('api.product.additional.images.store');
            Route::get('/product/{id}/additional-images', [AdditionalImageController::class, 'index'])->name('api.product.additional.images');
            Route::delete('/additional-images/{id}', [AdditionalImageController::class, 'destroy'])->name('api.additional.images.destroy');
            Route::post('/product/{id}/additional-images/order', [AdditionalImageController::class, 'updateOrder'])->name('api.product.additional.images.order');
            Route::post('/additional-images/{id}/select', [AdditionalImageController::class, 'setSelected'])->name('api.additional.images.select');
            
            Route::get('/discounts', [DiscountController::class, 'apiIndex'])->name('api.discounts.index');
            Route::get('/discounts/{id}', [DiscountController::class, 'apiShow'])->name('api.discounts.show');
            Route::post('/discounts', [DiscountController::class, 'apiStore'])->name('api.discounts.store');
            Route::match(['put', 'post'], '/discounts/{id}', [DiscountController::class, 'apiUpdate'])->name('api.discounts.update');
            Route::delete('/discounts/{id}', [DiscountController::class, 'apiDestroy'])->name('api.discounts.destroy');
            Route::get('/discounts/product/{productId}/active', [DiscountController::class, 'apiActiveProductDiscounts'])->name('api.discounts.product.active');
        });
    });

    // -----------------------------------------------------------------
    // PUBLIC AUTH ROUTES (ለማንኛውም ገባ ተጠቃሚ)
    // -----------------------------------------------------------------

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/api/cart/count', [CartController::class, 'getCartCount']);
    Route::get('/api/cart/total', [CartController::class, 'getCartTotal']);
    
    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::delete('/wishlist/remove/{product_id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/move-to-cart/{id}', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
    Route::get('/api/wishlist/count', [WishlistController::class, 'getWishlistCount']);

    // Customer Orders
    Route::get('/orders/my-orders', [ShipmentController::class, 'customerOrders'])->name('orders.customer');
    Route::get('/orders/{order}', [ShipmentController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [ShipmentController::class, 'cancel'])->name('orders.cancel');
    Route::match(['put', 'post'], '/orders/{order}/status', [ShipmentController::class, 'updateStatus'])->name('orders.update-status');

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
        Route::match(['put', 'post'], '/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::match(['put', 'post'], '/account-number', [SettingsController::class, 'updateAccountNumber'])->name('settings.account-number.update');
        Route::match(['put', 'post'], '/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
        Route::match(['put', 'post'], '/profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('settings.profile-picture.update');
        Route::delete('/account', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
    });
    
    // Verification Request (Accessible while unverified)
    Route::prefix('verification')->group(function () {
        Route::get('/request', [VerificationController::class, 'create'])->name('verification.request');
        Route::post('/submit', [VerificationController::class, 'store'])->name('verification.submit');
        Route::post('/upload-id-image', [VerificationController::class, 'uploadIdImage'])->name('verification.upload-id-image');
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