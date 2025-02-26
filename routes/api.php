<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //Category
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    //product
    Route::post('/addproduct', [ProductController::class, 'addproduct']);
    Route::get('/products', [ProductController::class, 'viewproduct']);
    Route::put('/product/{id}', [ProductController::class, 'updateproduct']);
    Route::delete('/product/{id}', [ProductController::class, 'deleteproduct']);
    

});

// Uncomment this route if you need to access the authenticated user directly
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');




