<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $postsCountByCategory = Category::withCount('posts')->get();
        $usersCount = User::count();
        $postsCount = Post::count();
        $commentsCount = Comment::count();

        return view('admin.dashboard', compact('postsCountByCategory','usersCount','postsCount','commentsCount'));
    }

    public function index()
    {
        $categories = Category::withCount('posts')->get();
        return view('admin.index', compact('categories'));
    }
}
