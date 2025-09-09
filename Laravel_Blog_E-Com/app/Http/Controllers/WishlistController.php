<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $ids = WishlistItem::where('user_id', Auth::id())
            ->pluck('product_id');

        $products = $ids->isNotEmpty()
            ? Product::with('category')->whereIn('id', $ids)->get()
            : collect();

        return view('wishlist.index', compact('products'));
    }

    public function toggle(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        if (!$productId) {
            return response()->json(['ok' => false, 'message' => 'Missing product_id'], 422);
        }

        $row = WishlistItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($row) {
            $row->delete();
            $inWishlist = false;
        } else {
            WishlistItem::firstOrCreate([
                'user_id'    => Auth::id(),
                'product_id' => $productId,
            ]);
            $inWishlist = true;
        }

        $count = WishlistItem::where('user_id', Auth::id())->count();

        // (optional) clear any session legacy keys so badges stay consistent
        session()->forget(['wishlist.items', 'wishlist.count']);

        return response()->json([
            'ok'          => true,
            'in_wishlist' => $inWishlist,
            'count'       => $count,
            'product_id'  => $productId,
        ]);
    }
}
