<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
// Protected routes (add middleware later)
// Route::middleware(['auth'])->group(function () {
//     // Your protected routes here
// });
// routes/web.php
Route::get('/test-db-connection', function () {
    \Log::info('=== TESTING DATABASE CONNECTION ===');

    try {
        // Test database connection
        $pdo = \DB::connection()->getPdo();
        \Log::info('✅ Database connection successful');

        // Test if we can select from users table
        $userCount = \DB::table('users')->count();
        \Log::info("✅ Users table exists. Total users: " . $userCount);

        // Test inserting a user
        $testData = [
            'username' => 'xampp_test_' . time(),
            'email' => 'xampp_test_' . time() . '@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'phone' => '+251911111111',
            'national_id' => 'xampp_test',
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        \Log::info('Attempting to insert test data:', $testData);

        $userId = \DB::table('users')->insertGetId($testData);

        \Log::info("✅ TEST INSERT SUCCESSFUL! User ID: " . $userId);

        return response()->json([
            'success' => true,
            'message' => 'Database test successful!',
            'user_count' => $userCount,
            'test_user_id' => $userId,
            'test_data' => $testData
        ]);

    } catch (\Exception $e) {
        \Log::error('❌ DATABASE TEST FAILED:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'database_info' => [
                'connection' => config('database.default'),
                'database' => config('database.connections.mysql.database'),
                'host' => config('database.connections.mysql.host'),
            ]
        ], 500);
    }
});
