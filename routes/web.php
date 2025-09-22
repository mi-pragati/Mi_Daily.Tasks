<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Editor\EditorHomeController;
use App\Http\Controllers\Editor\EditorCategoryController;
use App\Http\Controllers\Editor\EditorPostController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Editor\EditorProductController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\CustomerRegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\AdminProductCategoryController;
use App\Http\Middleware\AdminMiddleware;

require __DIR__.'/auth.php';

// ===========================
// Reader Routes (Public Access)
// ===========================

// Home shows categories (reader view)
Route::get('/blog', [CategoryController::class, 'index'])->name('blog.index');

// Reader Categories and Posts
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Reader Comments (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.mine');
    Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Routes for Products (Reader view)
Route::get('/', [HomeController::class, 'index'])->name('homi');

Route::get('/home', fn () => redirect()->route('homi'))->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/products/category/{category:slug}', [ProductController::class, 'filterByCategory'])->name('products.filter');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');

//Cart

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart',           [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{id}',[CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}',[CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart',         [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', function () {
    $cart = session('cart', []);
    return response()->json([
        'ok' => true,
        'count' => count($cart)
    ]);
})->name('cart.count');

Route::middleware('auth')->group(function () {
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/details', [CheckoutController::class, 'details'])->name('checkout.details');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');

Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])
    ->name('checkout.createPaymentIntent');

Route::post('/checkout/store-payment', [CheckoutController::class, 'storePayment'])->name('checkout.storePayment');

});

// Customer Registration
Route::middleware('guest')->group(function () {
    Route::get('/register/customer', [CustomerRegisterController::class, 'create'])
        ->name('customer.register');

    Route::post('/register/customer', [CustomerRegisterController::class, 'store'])
        ->name('customer.register.store');

});

//Customer Dashboard (For signed-in customers)

Route::middleware('auth')->group(function () {
    Route::get('/customer', [DashboardController::class, 'index'])->name('customer.dashboard');

      Route::get('/orders', [OrderController::class, 'index'])->name('customer.orders.index');
      Route::get('/orders/{order}', [OrderController::class, 'show'])->name('customer.orders.show');
      Route::get('/order/confirmation/{order}', [OrderController::class, 'confirmation'])
    ->name('customer.orders.confirmation');
        Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])
            ->name('customer.orders.reorder');

        Route::get('/orders/{order}/return', [OrderController::class, 'showReturnForm'])
            ->name('customer.orders.return');

        Route::post('/orders/{order}/return', [OrderController::class, 'submitReturn'])
        ->name('customer.orders.return.submit');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])
    ->name('wishlist.toggle');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])
    ->name('wishlist.count');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');


    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

     Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.update-name');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/request-otp', [ProfileController::class, 'requestOtp'])->name('profile.request-otp');
    Route::post('/profile/confirm-otp', [ProfileController::class, 'confirmOtp'])->name('profile.confirm-otp');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// ===========================
// Editor Routes (Restricted Access)
// ===========================

Route::prefix('editor')->name('editor.')->middleware('auth')->group(function () {
    // Editor Home
    Route::get('/', [EditorHomeController::class, 'index'])->name('home');

    // Editor Categories and Posts
    Route::get('/categories', [EditorCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [EditorCategoryController::class, 'show'])->name('categories.show');
    Route::post('/posts', [EditorPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/create', [EditorPostController::class, 'create'])->name('posts.create');
    Route::get('/posts/{post:slug}/edit', [EditorPostController::class, 'edit'])->name('posts.edit');
    Route::get('/posts/{post:slug}', [EditorPostController::class, 'show'])->name('posts.show');

    //Editor Profile
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.update-name');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::post('/profile/request-otp', [ProfileController::class, 'requestOtp'])->name('profile.request-otp');
Route::post('/profile/confirm-otp', [ProfileController::class, 'confirmOtp'])->name('profile.confirm-otp');

Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    // Admin Order Route

    Route::get('orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])
    ->name('orders.updateStatus');
    Route::patch('/orders/{order}/return', [App\Http\Controllers\Admin\OrderController::class, 'updateReturn'])
    ->name('orders.updateReturn');


    //Admin Settings 

    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');

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

Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.update-name');

Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::post('/profile/request-otp', [ProfileController::class, 'requestOtp'])->name('profile.request-otp');
Route::post('/profile/confirm-otp', [ProfileController::class, 'confirmOtp'])->name('profile.confirm-otp');

Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Admin Product Routes
    Route::resource('products', AdminProductController::class);
     /*   Route::get('/admin/products', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::post('/admin/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    */
    Route::resource('product-categories', AdminProductCategoryController::class)->parameters([
        'product-categories' => 'productCategory'
    ]);
    Route::get('products/category/{category:slug}', [AdminProductController::class, 'showCategoryProducts'])
    ->name('products.byCategory');

});

// ============================
// Logic Routes (Dashboard, etc.)
// ============================

Route::get('/dashboard', function () {
    abort_unless(auth()->check(), 403);

    return match (auth()->user()->role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'editor' => redirect()->route('editor.home'),
        default  => redirect()->route('homi'),
    };
})->middleware('auth')->name('dashboard');
