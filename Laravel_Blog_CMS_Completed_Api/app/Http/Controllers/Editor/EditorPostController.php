<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Post;

class EditorPostController extends Controller
{
    public function show(Post $post)
    {
        $post->load(['author','category','tags','comments.user']);

        $related = Post::where('category_id', $post->category_id)
            ->where('id', '<>', $post->id)
            ->latest()
            ->take(5)
            ->get();

        return view('editor.posts.show', compact('post','related'));
    }
}
