<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public API routes (for mobile apps, external services)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'status' => 'success',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Protected API routes (require Sanctum token)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // Admin verification route
    Route::post('/users/{id}/verify', [AuthController::class, 'verifyUser'])->middleware(['admin']);
});

// Fallback for undefined API routes
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found',
        'status' => 'error'
    ], 404);
});