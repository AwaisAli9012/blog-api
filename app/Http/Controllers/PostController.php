<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with('user', 'comments.user')->latest()->get();

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