<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->get();

        return Inertia::render('Posts', [
            'posts' => $posts,
            'auth' => auth()->user()
        ]);
    }

    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);

        return Inertia::render('Post', [
            'post' => $post,
            'auth' => auth()->user()
        ]);
    }

    public function apiIndex()
    {
        $posts = Post::with(['user','comments'])->latest()->get();

        return response()->json([
            'posts' => $posts,
            'auth' => auth()->user()
        ]);
    }

    public function apiShow($id)
    {
        $post = Post::with(['user','comments'])->findOrFail($id);

        return response()->json([
            'post' => $post,
            'auth' => auth()->user()
        ]);
    }
}
