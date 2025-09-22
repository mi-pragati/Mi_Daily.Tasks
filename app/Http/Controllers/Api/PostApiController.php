<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostApiController extends Controller
{
    public function index()
    {
        return response()->json(Post::with('category')->get());
    }

    public function show($id)
    {
        return response()->json(Post::with('category')->findOrFail($id));
    }
}
