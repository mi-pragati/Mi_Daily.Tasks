<?php

use Illuminate\Support\Facades\View;
use App\Models\Product;


class RouteServiceProvider extends ServiceProvider
{
public function boot()
{
    // Provide recently viewed products to all views
    View::composer('*', function ($view) {
        $recentlyViewedIds = session()->get('recently_viewed', []);
        $recentlyViewedProducts = Product::whereIn('id', $recentlyViewedIds)
                                         ->orderByRaw("FIELD(id," . implode(',', $recentlyViewedIds) . ")")
                                         ->get();
        $view->with('recentlyViewedProducts', $recentlyViewedProducts);
    });
}
}