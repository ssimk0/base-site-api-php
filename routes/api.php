<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageCategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UploadCategoryController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UploadTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:api'
])->group(function ($router) {
    Route::middleware(["can:editor,all"])->group(function () {
        Route::post("v1/articles", [ArticleCategoryController::class, 'create']);
        Route::put("v1/articles/{category}", [ArticleCategoryController::class, 'update']);
        Route::delete("v1/articles/{category}", [ArticleCategoryController::class, 'delete']);
        Route::post("v1/articles/{category:slug}", [ArticleController::class, 'create']);
        Route::put("v1/articles/{category:slug}/{article}", [ArticleController::class, 'update']);
        Route::delete("v1/articles/{category:slug}/{article}", [ArticleController::class, 'delete']);

        Route::post("v1/uploads/{type}/{category}", [UploadController::class, 'store']);
        Route::put("v1/uploads/{type}/{category}/{upload}", [UploadController::class, 'update']);
        Route::delete("v1/uploads/{type}/{category}/{upload}", [UploadController::class, 'delete']);
        Route::post("v1/uploads/{type}", [UploadCategoryController::class, 'store']);
        Route::put("v1/uploads/{type}/{category:id}", [UploadCategoryController::class, 'update']);
        Route::delete("v1/uploads/{type}/{category:id}", [UploadCategoryController::class, 'delete']);
    });

    Route::get('v1/auth/user', [AuthController::class, 'userInfo']);
    Route::middleware(["can:admin,all"])->group(function () {
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
Route::post('v1/auth/reset-password/{token}', [AuthController::class, 'resetPassword']);
Route::middleware(['middleware' => 'throttle:3,10'])->group(function () {
    Route::post('v1/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('v1/auth/login', [AuthController::class, 'login']);
    Route::post('v1/auth/register-user', [AuthController::class, 'register']);
    Route::post('v1/contact', function (Request $request) {
        $data = $request->validate([
            "name" => "required|min:3",
            "subject" => "required|min:3",
            "email" => "required|email",
            "message" => "required|min:10"
        ]);
        Mail::raw($data["message"], function($mail) use ($data) {
            $name = $data['name'];
            $subject = $data['subject'];
            $mail->from($data["email"])->to(env('CONTACT_MAIL'))->subject("Contact form mail from $name with subject: $subject");
        });

        return response()->json([]);
    });
});


Route::get('v1/pages', [PageCategoryController::class, 'list']);
Route::get('v1/pages/{category}', [PageController::class, 'list']);
Route::get('v1/pages/{category}/{page}', [PageController::class, 'detail']);

Route::get("v1/articles", [ArticleCategoryController::class, 'list']);
Route::get('v1/articles/{category:slug}', [ArticleController::class, 'list']);
Route::get('v1/articles/{category:slug}/{article:slug}', [ArticleController::class, 'detail']);

Route::get("v1/uploads/{type}", [UploadTypeController::class, 'list']);
Route::get("v1/uploads/{type}/{category}/latest", [UploadController::class, 'latest']);
Route::get("v1/uploads/{type}/{category}/{upload}/download", [UploadController::class, 'download']);
Route::get("v1/uploads/{type}/{category}/{upload}", [UploadController::class, 'detail']);
Route::get("v1/uploads/{type}/{category}", [UploadCategoryController::class, 'list']);


Route::get("v1/announcement", [AnnouncementController::class, 'active']);
