<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function show(Post $post)
{
    $post->load('comments.user'); // eager load user with comments
    return view('posts.show', compact('post'));
}

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

                return back()->with('success', 'Comment added successfully!');

    }
}
