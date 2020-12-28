<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageCategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UploadCategoryController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UploadTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:api'
])->group(function ($router) {
    Route::middleware(["can:editor,all"])->group(function() {
        Route::post("v1/articles", [ArticleController::class, 'create']);
        Route::put("v1/articles/{article}", [ArticleController::class, 'update']);
        Route::delete("v1/articles/{article}", [ArticleController::class, 'delete']);

        Route::post("v1/uploads/{type}/{category}", [UploadController::class, 'store']);
        Route::put("v1/uploads/{type}/{category}/{upload}", [UploadController::class, 'update']);
        Route::delete("v1/uploads/{type}/{category}/{upload}", [UploadController::class, 'delete']);
        Route::post("v1/uploads/{type}", [UploadCategoryController::class, 'store']);
        Route::put("v1/uploads/{type}/{category:id}", [UploadCategoryController::class, 'update']);
        Route::delete("v1/uploads/{type}/{category:id}", [UploadCategoryController::class, 'delete']);
    });

    Route::get('v1/auth/user', [AuthController::class, 'userInfo']);
    Route::middleware(["can:admin,all"])->group(function() {
        Route::put('v1/pages/{category}/{page}', [PageController::class, 'update']);
        Route::put('v1/pages/{page:id}', [PageController::class, 'update']);
        Route::post('v1/pages/{category}', [PageController::class, 'create']);
        Route::delete('v1/pages/{category}/{page}', [PageController::class, 'delete']);
        Route::delete('v1/pages/{page:id}', [PageController::class, 'delete']);


        Route::post("v1/announcement", [AnnouncementController::class, 'store']);
    });
});

Route::post('v1/auth/logout', [AuthController::class, 'logout']);
Route::post('v1/auth/refresh', [AuthController::class, 'refresh']);
Route::post('v1/auth/login', [AuthController::class, 'login']);
Route::post('v1/auth/register-user', [AuthController::class, 'register']);
Route::post('v1/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('v1/auth/reset-password/{token}', [AuthController::class, 'resetPassword']);

Route::get('v1/pages', [PageCategoryController::class, 'list']);
Route::get('v1/pages/{category}', [PageController::class, 'list']);
Route::get('v1/pages/{category}/{page}', [PageController::class, 'detail']);

Route::get('v1/articles', [ArticleController::class, 'list']);
Route::get('v1/articles/{article:slug}', [ArticleController::class, 'detail']);

Route::get("v1/uploads/{type}", [UploadTypeController::class, 'list']);
Route::get("v1/uploads/{type}/{category}/latest", [UploadController::class, 'latest']);
Route::get("v1/uploads/{type}/{category}/{upload}/download", [UploadController::class, 'download']);
Route::get("v1/uploads/{type}/{category}/{upload}", [UploadController::class, 'detail']);
Route::get("v1/uploads/{type}/{category}", [UploadCategoryController::class, 'list']);


Route::get("v1/announcement", [AnnouncementController::class, 'active']);
