<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Editor\EditorHomeController;
use App\Http\Controllers\Editor\EditorCategoryController;
use App\Http\Controllers\Editor\EditorPostController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Middleware\AdminMiddleware;


require __DIR__.'/auth.php';

//READERS

// Home shows categories (reader view)
Route::get('/', [CategoryController::class, 'index'])->name('home');

// Reader categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Reader posts
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// Reader comments (auth required)
Route::middleware('auth')->group(function () {
    Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

//EDITORS

Route::prefix('editor')->name('editor.')->middleware('auth')->group(function () {

    // Editor home (same content as reader home but different layout/header)
    Route::get('/', [EditorHomeController::class, 'index'])->name('home');

    // Editor categories (same content as reader, different view/layout)
    Route::get('/categories', [EditorCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [EditorCategoryController::class, 'show'])->name('categories.show');

        Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');

        Route::get('/posts/{post:slug}', [EditorPostController::class, 'show'])
        ->name('posts.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy');

    // My posts + profile
    Route::get('/me/posts', [PostController::class, 'mine'])->name('posts.mine');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.update-name');
Route::post('/profile/request-otp', [ProfileController::class, 'requestOtp'])->name('profile.request-otp');
Route::post('/profile/confirm-otp', [ProfileController::class, 'confirmOtp'])->name('profile.confirm-otp');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//ADMIN

Route::get('/admin/register', [AdminRegisterController::class, 'showRegisterForm'])->name('admin.register.form');
Route::post('/admin/register', [AdminRegisterController::class, 'register'])->name('admin.register');

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    //Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category:slug}', [AdminCategoryController::class, 'show'])
        ->name('categories.show');


    // All posts
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [\App\Http\Controllers\Admin\AdminPostController::class, 'create'])->name('posts.create');
    Route::post('/posts',        [\App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post:slug}/edit', [\App\Http\Controllers\PostController::class, 'edit'])->name('posts.edit');
    Route::get('/posts/{post:slug}', [\App\Http\Controllers\Admin\AdminPostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post:slug}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/profile',        [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',        [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/name',  [\App\Http\Controllers\ProfileController::class, 'updateName'])->name('profile.update-name');
    Route::post('/profile/request-otp', [\App\Http\Controllers\ProfileController::class, 'requestOtp'])->name('profile.request-otp');
    Route::post('/profile/confirm-otp', [\App\Http\Controllers\ProfileController::class, 'confirmOtp'])->name('profile.confirm-otp');
    Route::delete('/profile',     [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

//LOGIC
Route::get('/dashboard', function () {
    abort_unless(auth()->check(), 403);

    return match (auth()->user()->role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'editor' => redirect()->route('editor.home'),
        default  => redirect()->route('home'),
    };
})->middleware('auth')->name('dashboard');

