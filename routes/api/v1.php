<?php

use App\Http\Controllers\Api\V1\AdController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
});
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['throttle:6,1'])
    ->name('verification.verify');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// category routes

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

// ad routes
Route::get('/ads', [AdController::class, 'index'])->name('ads.index');
Route::get('/ads/{ad}', [AdController::class, 'show'])->name('ads.show');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ads', [AdController::class, 'store'])->name('ads.store');
});