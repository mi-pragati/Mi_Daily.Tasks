<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
    $request->validate([
        'rating'  => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    // Check if user purchased this product
    $hasBought = OrderItem::whereHas('order', function ($q) {
        $q->where('user_id', Auth::id())
          ->where('status', 'Completed');
    })->where('product_id', $product->id)->exists();

    if (!$hasBought) {
        return response()->json([
            'ok' => false,
            'message' => 'You can only review products you purchased.'
        ], 403);
    }

    $review = Review::updateOrCreate(
        ['user_id' => Auth::id(), 
        'product_id' => $product->id,
    ],
        ['rating' => $request->rating, 
        'comment' => $request->comment,
        ]
    );

    return response()->json([
        'ok' => true,
        'review' => [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'user_name' => $review->user->name,
        ]
    ]);
}


    public function destroy(Product $product, Review $review)
{
    $this->authorize('delete', $review); // Laravel Policy check
    $review->delete();

    return back()->with('success', 'Review deleted.');
}

}
