<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\Auth\AuthController;

Route::middleware('api')->prefix('auth')->group(function () {

    // ── Public
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);

    // ── Protected
    Route::middleware('auth:api')->group(function () {
        Route::get('me',  [AuthController::class, 'profile']);
        Route::post('logout',  [AuthController::class, 'logout']);
    });
});
