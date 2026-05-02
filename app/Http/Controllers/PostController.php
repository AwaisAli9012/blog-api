<?php

namespace App\Http\Controllers;

use App\Models\Post; // we need the Post model

class PostController extends Controller
{
   public function show($id)
{
    // Find the post by its ID
   return $post = \App\Models\Post::findOrFail($id);
    

    // Return it as JSON
    return response()->json($post);
}

}
