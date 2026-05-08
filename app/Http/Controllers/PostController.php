<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    // Inertia frontend routes
    public function index()
    {
        $posts = Post::with(['user','comments.user'])->latest()->get();

        return Inertia::render('Posts', [
            'posts' => $posts,
            'auth' => auth()->user()
        ]);
    }

    public function show($id)
    {
        $post = Post::with(['user','comments.user'])->findOrFail($id);

        return Inertia::render('Post', [
            'post' => $post,
            'auth' => auth()->user()
        ]);
    }

    // API JSON routes
    public function apiIndex()
    {
        $posts = Post::with(['user','comments.user'])->latest()->get();

        return response()->json([
            'posts' => $posts,
            'auth' => auth()->user()
        ]);
    }

    public function apiShow($id)
    {
        $post = Post::with(['user','comments.user'])->findOrFail($id);

        return response()->json([
            'post' => $post,
            'auth' => auth()->user()
        ]);
    }

    // Other CRUD methods (store, update, destroy) remain unchanged
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create($validated);

        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update($validated);

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(null, 204);
    }
}
