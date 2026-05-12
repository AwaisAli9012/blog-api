<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Welcome page
Route::get('/', function () { return redirect('/posts'); });

// Public routes - anyone can view posts
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/create', [PostController::class, 'create']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// Protected routes - must be logged in
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $posts = \App\Models\Post::with(['comments'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($post) {
                return [
                    'id'            => $post->id,
                    'title'         => $post->title,
                    'content'       => $post->content,
                    'comment_count' => $post->comments->count(),
                    'created_at'    => $post->created_at->diffForHumans(),
                ];
            });
        return Inertia::render('Dashboard', [
            'auth'        => $user,
            'posts'       => $posts,
            'totalPosts'  => $posts->count(),
            'totalComments' => $posts->sum('comment_count'),
        ]);
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::put('/comments/{id}',[CommentController::class,'update']);
});

require __DIR__.'/auth.php';
Route::get('/run-github-sync', function() {
    Artisan::call('github:sync');
    return Artisan::output();
});
Route::get('/debug-github', function() {
    $token = env('GITHUB_TOKEN');
    $username = env('GITHUB_USERNAME');
    $url = 'https://api.github.com/users/' . $username . '/events';
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/vnd.github+json',
        'X-GitHub-Api-Version' => '2022-11-28',
    ])->get($url);
    return response()->json(['url' => $url, 'status' => $response->status(), 'data' => $response->json()]);
});
