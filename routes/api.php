<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/register', \App\Http\Controllers\Api\v1\Auth\RegisterController::class);
        Route::post('/login', \App\Http\Controllers\Api\v1\Auth\LoginController::class);
        Route::post('/logout', \App\Http\Controllers\Api\v1\Auth\LogoutController::class)
            ->middleware('auth:sanctum');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/posts', [\App\Http\Controllers\Api\v1\PostController::class, 'index']);
        Route::post('/posts', [\App\Http\Controllers\Api\v1\PostController::class, 'store']);
        Route::delete('/posts/{post}', [\App\Http\Controllers\Api\v1\PostController::class, 'destroy'])
            ->missing(function () {
                return response()->json([
                    'message' => 'Post not found',
                ]);
            });
    });
});
