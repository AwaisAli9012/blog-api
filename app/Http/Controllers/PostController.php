<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Post; // we need the Post model

class PostController extends Controller
{

public function store(Request $request)
{
    // Validate incoming data
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    // Create a new post using the validated data
    $post = Post::create($validated);

    // Return the new post as JSON with status code 201 (Created)
    return response()->json($post, 201);
}

public function destroy($id)
{
    $post = Post::findOrFail($id);
    $post->delete();

    return response()->json(null, 204);
}


public function index()
{
    // Fetch all posts and return them as JSON
    $posts = Post::all();

    return response()->json($posts);
}

   public function show($id)
{
    // Find the post by its ID
   $post = \App\Models\Post::findOrFail($id);
    

    // Return it as JSON
    return response()->json($post);
}

}
