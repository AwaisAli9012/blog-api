<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::post('/posts', [PostController::class, 'store']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);Route::get('/posts', [App\Http\Controllers\PostController::class, 'apiIndex']);
Route::get('/posts/{id}', [App\Http\Controllers\PostController::class, 'apiShow']);

Route::post('/auto-post', function(Illuminate\Http\Request $request) {
    $secret = $request->header('X-Secret-Key');
    return response()->json(['secret' => env('AUTO_POST_SECRET'), 'received' => $secret]);
    $user = App\Models\User::first();
    $post = App\Models\Post::create([
        'title' => $request->input('title'),
        'content' => $request->input('content'),
        'slug' => 'auto-' . time(),
        'user_id' => $user->id,
    ]);
    return response()->json(['success' => true, 'post_id' => $post->id]);
});
