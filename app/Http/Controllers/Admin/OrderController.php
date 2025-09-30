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

    // 🔍 Search filter (fuzzy search)
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            // Order fields
            $q->where('id', $search) // exact match for ID
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");

            // User fields
            $q->orWhereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
            });

            // Order items & products
            $q->orWhereHas('orderItems', function ($itemQuery) use ($search) {
                $itemQuery->where('title', 'like', "%{$search}%")
                          ->orWhereHas('product', function ($productQuery) use ($search) {
                              $productQuery->where('title', 'like', "%{$search}%");
                          });
            });
        });
    }

    // 🏷 Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Paginate
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

    $orders = $query->take(12)->get();

    return response()->json($orders);
}

public function exportCsv(Request $request)
{
    $query = \App\Models\Order::with('user', 'orderItems.product');

    // 🔍 Fuzzy search (same as index)
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
              })
              ->orWhereHas('orderItems', function ($itemQuery) use ($search) {
                  $itemQuery->where('title', 'like', "%{$search}%")
                            ->orWhereHas('product', function ($productQuery) use ($search) {
                                $productQuery->where('title', 'like', "%{$search}%");
                            });
              });
        });
    }

    // 🏷 Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $orders = $query->orderByDesc('created_at')->get();

    $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $columns = ['Order ID', 'Customer', 'Email', 'Phone', 'Address', 'Total', 'Order Status', 'Delivery Status', 'Order Date'];

    $callback = function() use ($orders, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($orders as $order) {
            $return = $order->latestReturn;
            $deliveryStatus = $return->status ?? $order->delivery_status ?? 'Pending';

            fputcsv($file, [
                $order->id,
                $order->user?->name ?? 'Guest',
                $order->email,
                $order->phone,
                $order->address,
                $order->final_total,
                $order->status,
                $deliveryStatus,
                $order->created_at->format('d-m-Y H:i:s'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


}
