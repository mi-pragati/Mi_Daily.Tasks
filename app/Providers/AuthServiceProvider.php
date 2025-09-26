<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Review;
use App\Policies\ReviewPolicy;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use App\Policies\CategoryPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        Category::class => CategoryPolicy::class,
        Comment::class=>CommentPolicy::class,
            \App\Models\Product::class => \App\Policies\ProductPolicy::class,
            \App\Models\Order::class => \App\Policies\OrderPolicy::class,
    \App\Models\Review::class => \App\Policies\ReviewPolicy::class,


    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define role-based gates
        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('isEditor', function ($user) {
            return $user->role === 'editor';
        });

        Gate::define('isReader', function ($user) {
            return $user->role === 'reader';
        });
    }
}

