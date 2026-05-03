<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// Public routes - anyone can view posts
Route::get('/', function () {
    return view('welcome');
});
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// Protected routes - must be logged in
Route::middleware('auth')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
});

require __DIR__.'/auth.php';