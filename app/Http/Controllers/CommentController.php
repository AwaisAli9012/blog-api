<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'body' => 'required|min:1|max:1000',
        ]);

        Comment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        return back();
    }
}
