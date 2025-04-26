<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\Auth\AuthController;
use App\Http\Controllers\api\v1\Department\DepartmentController;
use App\Http\Controllers\api\v1\Employee\EmployeeController;

Route::middleware('api')->group(function () {

    // ── Public
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('employees', EmployeeController::class);

    // ── Protected
    Route::middleware('auth:api')->group(function () {
        Route::get('me',  [AuthController::class, 'profile']);
        Route::post('logout',  [AuthController::class, 'logout']);
    });
});
