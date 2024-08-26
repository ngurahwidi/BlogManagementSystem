<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::group(['middleware' => 'auth.jwt'], function () {
    //Route Article
    Route::get('/articles', [ArticleController::class, 'get']);
    Route::get('/articles/{id}', [ArticleController::class, 'getById']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::post('/articles/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']); 

    //Route Category
    Route::get('/categories', [CategoryController::class, 'get']);
    Route::get('/categories/{id}', [CategoryController::class, 'getById']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    //Route Tag
    Route::get('/tags', [TagController::class, 'get']);
    Route::get('/tags/{id}', [TagController::class, 'getById']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth.jwt')->post('/logout', [AuthController::class, 'logout']);

