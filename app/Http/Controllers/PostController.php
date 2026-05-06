<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function index()
 
    public function apiIndex()
    {
        $posts = Post::with(["user","comments"])->latest()->get();
        return response()->json([
            "posts" => $posts,
            "auth" => auth()->user()
        ]);
    }

    {
        $posts = Post::with(["user","comments"])->latest()->get();

        return response()->json([
            'posts' => $posts,
            'auth' => auth()->user()
        ]);
    }

    public function show($id)
 
    public function apiShow($id)
    {
        $post = Post::with(["user","comments"])->findOrFail($id);
        return response()->json([
            "post" => $post,
            "auth" => auth()->user()
        ]);
    }

    {
    $post = Post::with('user')->findOrFail($id);
    return Inertia::render('Post', [
        'post' => $post,
        'auth' => auth()->user()
    ]);
    }

    public function store(Request $request)
   {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
    $validated['user_id'] = auth()->id();

    Post::create($validated);

    return redirect('/posts');
   }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update($validated);

        return redirect('/posts');
    }

   public function destroy($id)
{
    $post = Post::findOrFail($id);
    if ($post->user_id !== auth()->id()) {
        abort(403);
    }
    $post->delete();
    return redirect('/posts');
}
}