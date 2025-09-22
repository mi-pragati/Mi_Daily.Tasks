<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get current cart items
     */
    protected function getCartItems(): array
    {
        if (Auth::check()) {
            $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();
            if (!$cart) return [];

            $items = [];
            foreach ($cart->items as $row) {
                $p = $row->product;
                $items[$row->product_id] = [
                    'id'    => $row->product_id,
                    'title' => $p->title ?? $p->name ?? 'Product',
                    'price' => (float) $row->price,
                    'qty'   => (int) $row->qty,
                    'image' => $p->image_url ?? ($p->image ? asset('storage/' . $p->image) : null),
                ];
            }
            return $items;
        }
        return session()->get('cart.items', []);
    }

    /**
     * Calculate totals for the cart
     */
    protected function totals(array $items): array
    {
        $subtotal = collect($items)->reduce(fn($c, $r) => $c + ($r['price'] * $r['qty']), 0);
        return ['subtotal' => $subtotal, 'total' => $subtotal];
    }

    /**
     * Show checkout page
     */
    public function index()
    {
        $items = $this->getCartItems();
        $totals = $this->totals($items);
        return view('checkout.index', compact('items', 'totals'));
    }

    /**
     * Place a new order
     */
    public function placeOrder(Request $request)
    {
        // Force JSON response
        $request->headers->set('Accept', 'application/json');

        // Validate inputs
        try {
            $request->validate([
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|max:255',
                'address'        => 'required|string|max:500',
                'phone'          => 'required|string|max:20',
                'payment_method' => 'required|string|in:cod,stripe',
                'stripe_method'  => 'nullable|string|in:card',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $subtotal = $cart->items->sum(fn($i) => $i->price * $i->qty);
        $total = $subtotal;

        // Create order
        $order = Order::create([
            'user_id'        => Auth::id(),
            'name'           => $request->name,
            'email'          => $request->email,
            'address'        => $request->address,
            'phone'          => $request->phone,
            'subtotal'       => $subtotal,
            'total'          => $total,
            'status'         => 'Pending',   // Admin control
            'delivery_status'=> 'Pending',   // Customer control - new order starts as Pending
            'payment_method' => 'N/A',
        ]);

        // Create order items & update stock
        foreach ($cart->items as $item) {
            $product = $item->product;

            if (!$product || $product->stock < $item->qty) {
                return response()->json(['error' => "{$product->title} is out of stock"], 400);
            }

            // Reduce stock
            $product->stock -= $item->qty;
            $product->save();

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'title'      => $product->title ?? 'Product',
                'price'      => $item->price,
                'qty'        => $item->qty,
                'subtotal'   => $item->price * $item->qty,
            ]);
        }

        // ---------------- COD ----------------
        if ($request->payment_method === 'cod') {
            $order->update(['payment_method' => 'cod']);

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            return response()->json([
                'success'  => true,
                'redirect' => route('customer.orders.confirmation', $order->id)
            ]);
        }

        // ---------------- Stripe ----------------
        if ($request->payment_method === 'stripe' && $request->stripe_method === 'card') {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => intval(round($total * 100)),
                'currency' => 'inr',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id'  => Auth::id(),
                ],
            ]);

            $order->update([
                'payment_method' => 'stripe - card',
                'status' => 'Pending',
                'payment_id' => $intent->id,
            ]);

            return response()->json([
                'clientSecret' => $intent->client_secret,
                'orderId'      => $order->id,
            ]);
        }
    }

    /**
     * Store payment after Stripe confirmation
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_id' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'status' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $payment = Payment::create([
            'order_id' => $request->order_id,
            'payment_id' => $request->payment_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
        ]);

        // Clear cart after successful payment
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }

        return response()->json(['success' => true, 'payment' => $payment]);
    }
}
