<?php
namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
class PostController extends Controller
{
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
    public function apiIndex()
    {
        $posts = Post::with(['user','comments.user'])->latest()->get();
        return response()->json(['posts' => $posts, 'auth' => auth()->user()]);
    }
    public function apiShow($id)
    {
        $post = Post::with(['user','comments.user'])->findOrFail($id);
        return response()->json(['post' => $post, 'auth' => auth()->user()]);
    }
    public function create()
    {
        return Inertia::render('PostCreate', [
            'auth' => auth()->user()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
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
        if(auth()->id() !== $post->user_id){
            abort(403);
        }
        $post->update($validated);
        return redirect('/posts');
    }

    public function destroy($id)
    {
    $post = Post::findOrFail($id);
        if(auth()->id() !== $post->user_id){
            abort(403);
        }
        $post->delete();
        return redirect('/posts');
    }
}
