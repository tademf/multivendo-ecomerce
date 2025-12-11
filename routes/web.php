<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use Inertia\Inertia;

// ========== PUBLIC ROUTES ==========
// Home
Route::get('/', [WebController::class, 'home'])->name('home');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

// Public product routes
Route::get('/public-products', [WebController::class, 'products'])->name('public.products');
Route::get('/products/{id}', [WebController::class, 'productDetail'])->name('product.detail');
Route::get('/cart', [WebController::class, 'cart'])->name('cart');

// Add test data route (for testing categories/tags)
Route::get('/add-test-data', [WebController::class, 'addTestData'])->name('add.test.data');

// Add API route for categories (to fix the 404 error)
Route::get('/api/categories', function () {
    return \App\Models\Category::all();
})->name('api.categories');

// ========== PROTECTED ROUTES (require authentication) ==========
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [WebController::class, 'profile'])->name('profile');
    Route::post('/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
    
    // Products Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/my-products/{product}', [ProductController::class, 'show'])->name('products.show');
    
    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::delete('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
    
    // ========== PAYMENT ROUTES ==========
    Route::get('/submit-payment', function () {
        return inertia('PaymentPage');
    })->name('payment.submit');
    
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    // ====================================
    
    // ========== ORDER ROUTES ==========
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show'); // Optional detail page
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    // ==================================
    
    // Shipments Management
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update');
    Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.updateStatus');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');
    
    // Shipment Actions
    Route::post('/shipments/{shipment}/cancel', [ShipmentController::class, 'cancel'])->name('shipments.cancel');
    Route::post('/shipments/{shipment}/mark-shipped', [ShipmentController::class, 'markAsShipped'])->name('shipments.mark-shipped');
    Route::post('/shipments/{shipment}/mark-delivered', [ShipmentController::class, 'markAsDelivered'])->name('shipments.mark-delivered');
    
    // Messages
    Route::get('/messages', [WebController::class, 'messages'])->name('messages');
    
    // Notifications
    Route::get('/notifications', [WebController::class, 'notifications'])->name('notifications');
    
    // Settings
    Route::get('/settings', [WebController::class, 'settings'])->name('settings');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ========== API ROUTES (for AJAX uploads) ==========
Route::middleware(['auth'])->group(function () {
    Route::post('/api/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture']);
});

// ========== FALLBACK ROUTE ==========
Route::fallback(function () {
    return redirect('/');
});