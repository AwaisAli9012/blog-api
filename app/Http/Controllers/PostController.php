<?php

namespace App\Http\Controllers;

use App\Models\Post; // we need the Post model

class PostController extends Controller
{

public function index()
{
    // Get all posts
    $posts=\App\Models\Post::all();

    // Return them as JSON
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
