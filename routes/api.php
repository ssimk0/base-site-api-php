<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'auth:api'
], function ($router) {
    Route::post('v1/auth/login', [AuthController::class, 'login']);
    Route::post('v1/auth/register-user', [AuthController::class, 'register']);
    Route::post('v1/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('v1/auth/reset-password/{token}', [AuthController::class, 'resetPassword']);
    Route::post('v1/auth/logout', [AuthController::class, 'logout']);
    Route::post('v1/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('v1/auth/user', [AuthController::class, 'userInfo']);
});
