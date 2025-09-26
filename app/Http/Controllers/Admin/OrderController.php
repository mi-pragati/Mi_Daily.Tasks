<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders with search and filter.
     */
    public function index(Request $request)
    {
        // Start query with relationships
        $query = Order::with('user', 'orderItems.product');

        // 🔍 Search filter (partial match)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', $search) // exact match for order ID
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('orderItems', function ($itemQuery) use ($search) {
                      $itemQuery->where('title', 'like', "%{$search}%")
                                ->orWhereHas('product', function ($productQuery) use ($search) {
                                    $productQuery->where('title', 'like', "%{$search}%");
                                });
                  });
            });
        }

        // 🏷 Status filter (optional)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginate instead of get() for performance
        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show a specific order with its details and items.
     */
    public function show(Order $order)
    {
        // Load related products for each order item
        $order->load('orderItems.product', 'user');

        return view('admin.orders.show', compact('order'));
    }
    public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:Pending,Completed',
    ]);

    $order->status = $request->status;
    $order->save();

    return redirect()->back()->with('success', 'Order status updated successfully!');
}

public function updateReturn(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:Return Pending,Returned,Rejected',
    ]);

    if ($order->returns) {
        $return = $order->returns instanceof \Illuminate\Database\Eloquent\Collection
                    ? $order->returns->last()
                    : $order->returns;

        $return->status = $request->status;
        $return->save();

        return redirect()->back()->with('success', 'Return status updated successfully.');
    }

    return redirect()->back()->with('error', 'No return found for this order.');
}

public function recentOrders(Request $request)
{
    $query = Order::with('user')->latest();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $orders = $query->take(10)->get();

    return response()->json($orders);
}



}
