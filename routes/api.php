<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(base_path('routes/api/v1.php'));

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
