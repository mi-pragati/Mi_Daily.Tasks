<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderReturn;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('orderItems.product')
            ->where('user_id', Auth::id());

        if ($search = $request->input('search')) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(orders.id) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(orders.name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(orders.email) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(orders.phone) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('orderItems', function ($itemQuery) use ($search) {
                      $itemQuery->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                                ->orWhereHas('product', function ($productQuery) use ($search) {
                                    $productQuery->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
                                });
                  });
            });
        }

        // Status filter is ignored; always Pending
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.orders.index', compact('orders'));

        $query = Order::with(['orderItems.product', 'returns'])
    ->where('user_id', Auth::id());

    }

    public function confirmation(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        // Force status to Pending
        $order->update([
            'status' => Order::STATUS_PENDING
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }

        $order->load('orderItems');

        return view('customer.orders.confirmation', compact('order'))
            ->with('success', 'Order placed successfully!');
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $order->load('orderItems.product');

        return view('customer.orders.show', compact('order'));
    }

    public function store(Request $request)
{
    $subtotal = 0;

    foreach ($request->items as $item) {
        $product = Product::find($item['product_id']);
        $subtotal += $product->price * $item['qty'];
    }

    $discount = $request->discount ?? 0; // coupon discount
    $total = $subtotal - $discount;

    $order = Order::create([
        'user_id' => Auth::id(),
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total' => $total,
        'coupon_code' => $request->coupon_code ?? null,
        'status' => Order::STATUS_PENDING,
        'payment_method' => 'N/A',
    ]);

    foreach ($request->items as $item) {
        $product = Product::find($item['product_id']);
        $order->orderItems()->create([
            'product_id' => $product->id,
            'qty' => $item['qty'],
            'price' => $product->price,
            'title' => $product->title,
            'subtotal' => $product->price * $item['qty'],
            'image' => $product->image,
        ]);
    }

    return redirect()->route('customer.orders.confirmation', $order->id);
}


   public function reorder(Request $request, $orderId)
{

        $userId = Auth::id();

    $oldOrder = Order::with('orderItems')->findOrFail($orderId);

    // ✅ Mark the original order as re-ordered
    $oldOrder->update(['is_reordered' => true]);

    // Clear current cart
    Cart::where('user_id', $userId)->delete();
    // ✅ Add the items to the current cart
    $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

    foreach ($oldOrder->orderItems as $item) {
        $cart->items()->create([
            'product_id' => $item->product_id,
            'qty' => $item->qty,
            'price' => $item->price,
        ]);
    }

    return redirect()->route('cart.index')
        ->with('success', 'Products from this order have been added to your cart!');
}

public function showReturnForm(Order $order)
{
    $this->authorize('view', $order); // Ensure customer owns the order
        $order->load('orderItems.product');

    return view('customer.orders.return', compact('order'));
}

// Handle return request
public function submitReturn(Request $request, Order $order)
{
    $this->authorize('view', $order);

    $request->validate([
        'message' => 'required|string|max:1000',
        'image'   => 'nullable|image|max:2048', // optional proof image
    ]);

    // Save the return request
    $return = $order->returns()->create([
        'message' => $request->message,
        'image' => $request->hasFile('image') ? $request->file('image')->store('returns', 'public') : null,
        'status' => 'Pending', // admin approval pending
    ]);
    $order->update([
    'return_status' => 'Pending',
]);

    return redirect()->route('customer.orders.index')
        ->with('success', 'Return request submitted. It will be initiated once admin allows.');
}
}

