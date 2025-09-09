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
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Editor\EditorProductController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProductCategoryController;
use App\Http\Middleware\AdminMiddleware;

require __DIR__.'/auth.php';

// ===========================
// Reader Routes (Public Access)
// ===========================

// Home shows categories (reader view)
Route::get('/', [CategoryController::class, 'index'])->name('home');

// Reader Categories and Posts
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Reader Comments (auth required)
Route::middleware('auth')->group(function () {
    Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Shop Routes for Products (Reader view)
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ProductController::class, 'show'])->name('shop.show');

// ===========================
// Editor Routes (Restricted Access)
// ===========================

Route::prefix('editor')->name('editor.')->middleware('auth')->group(function () {
    // Editor Home
    Route::get('/', [EditorHomeController::class, 'index'])->name('home');

    // Editor Categories and Posts
    Route::get('/categories', [EditorCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [EditorCategoryController::class, 'show'])->name('categories.show');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::get('/posts/{post:slug}', [EditorPostController::class, 'show'])->name('posts.show');
});

// Editor Product Routes
Route::middleware(['auth', 'role:editor,admin'])->prefix('editor')->name('editor.')->group(function () {
    Route::resource('products', EditorProductController::class);
});

// ============================
// Admin Routes (Restricted Access)
// ============================

// Admin Registration Routes
Route::get('/admin/register', [AdminRegisterController::class, 'showRegisterForm'])->name('admin.register.form');
Route::post('/admin/register', [AdminRegisterController::class, 'register'])->name('admin.register');

// Admin Panel Routes (Requires Authentication and Admin Role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Category Routes
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category:slug}', [AdminCategoryController::class, 'show'])->name('categories.show');

    // Admin Post Routes
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [AdminPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [AdminPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post:slug}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
    Route::get('/posts/{post:slug}', [AdminPostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post:slug}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

    // Admin Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.update-name');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Product Routes
    Route::resource('products', AdminProductController::class);
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::resource('product-categories', AdminProductCategoryController::class)->parameters([
        'product-categories' => 'productCategory'
    ]);
});

// ============================
// Logic Routes (Dashboard, etc.)
// ============================

Route::get('/dashboard', function () {
    abort_unless(auth()->check(), 403);

    return match (auth()->user()->role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'editor' => redirect()->route('editor.home'),
        default  => redirect()->route('home'),
    };
})->middleware('auth')->name('dashboard');
