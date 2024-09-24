<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FarmerController;

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes for authenticated users
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Farmer routes
    Route::get('/categories', [FarmerController::class, 'index']);
    Route::post('/categories', [FarmerController::class, 'store']);
    Route::get('/categories/{id}', [FarmerController::class, 'show']);
    Route::put('/categories/{id}', [FarmerController::class, 'update']);
    Route::delete('/categories/{id}', [FarmerController::class, 'destroy']);
});

// Uncomment this route if you need to access the authenticated user directly
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
