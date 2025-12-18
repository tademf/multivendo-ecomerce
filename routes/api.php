<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public API routes
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'status' => 'success',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Authentication API routes
Route::post('/register', [AuthController::class, 'registerApi']);
Route::post('/login', [AuthController::class, 'loginApi']);

// Protected API routes (require Sanctum token)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logoutApi']);
    Route::get('/user', [AuthController::class, 'me']);
});

// Fallback for undefined API routes
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found',
        'status' => 'error'
    ], 404);
});