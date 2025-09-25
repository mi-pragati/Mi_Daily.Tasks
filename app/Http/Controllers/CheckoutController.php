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
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail; 
use App\Models\Coupon;

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
     * Calculate cart total
     */
    protected function getCartTotal(): float
    {
        $items = $this->getCartItems();
        return collect($items)->reduce(fn($c, $r) => $c + ($r['price'] * $r['qty']), 0);
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
        $coupons = Coupon::where('expiry_date', '>=', now())
                 ->orWhereNull('expiry_date')
                 ->get();
        return view('checkout.index', compact('items', 'totals', 'coupons'));
    }

    /**
     * Place a new order
     */
    public function placeOrder(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'country'        => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'pincode'        => 'required|string|max:20',
            'street'         => 'required|string|max:255',
            'payment_method' => 'required|string|in:cod,stripe',
            'stripe_method'  => 'nullable|string|in:card',
        ]);

        $fullAddress = "{$request->street}, {$request->city}, {$request->state}, {$request->country} - {$request->pincode}";

        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // --- Use session values for totals ---
        $subtotal       = $this->getCartTotal();
        $discount       = session('discount', 0);
        $finalTotal     = session('final_total', $subtotal - $discount);
        $appliedCoupon  = session('coupon_code', null);

        // Create order
        $order = Order::create([
            'user_id'        => Auth::id(),
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'address'        => $fullAddress,
            'subtotal'       => $subtotal,
            'total'          => $subtotal,
            'discount'       => $discount,
            'final_total'    => $finalTotal,
            'coupon_code'    => $appliedCoupon,
            'status'         => 'Pending',
            'delivery_status'=> 'Pending',
            'payment_method' => 'N/A',
        ]);

        // Create order items & update stock
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product || $product->stock < $item->qty) {
                return response()->json(['error' => "{$product->title} is out of stock"], 400);
            }

            Mail::to($order->email)->send(new OrderPlacedMail($order));

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

    $cart->items()->delete();
    $cart->delete();
    session()->forget(['coupon_code', 'discount', 'final_total']);

    return response()->json([
        'success'  => true,
        'redirect' => route('customer.orders.confirmation', $order->id),
        'orderId'  => $order->id   // add this for JS
    ]);
}


        // ---------------- Stripe ----------------
        if ($request->payment_method === 'stripe' && $request->stripe_method === 'card') {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => intval(round($finalTotal * 100)),
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
                'discount' => $discount,
                'final_total' => $finalTotal
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

        $order = Order::find($request->order_id);
        $order->update([
            'payment_id' => $request->payment_id,
            'payment_method' => $request->payment_method,
            'status' => 'Paid',
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }
        session()->forget(['coupon_code', 'discount', 'final_total']);

        return response()->json(['success' => true, 'payment' => $payment]);
    }

    /**
     * Show all coupons
     */
    public function showCoupons()
    {
        $coupons = Coupon::where('expiry_date', '>=', now())->orWhereNull('expiry_date')->get();
        return view('customer.coupons', compact('coupons'));
    }

    /**
     * Apply a coupon
     */
    public function applyCoupon(Request $request)
    {
        $coupon = Coupon::where('code', $request->coupon_code)
            ->where(function($q){
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon'
            ]);
        }

        $cartTotal = $this->getCartTotal();
        $discount = ($coupon->type === 'fixed') ? min($coupon->value, $cartTotal) : ($coupon->value / 100) * $cartTotal;
        $finalTotal = max($cartTotal - $discount, 0);

        // store in session
        session([
            'coupon_code' => $coupon->code,
            'discount' => $discount,
            'final_total' => $finalTotal
        ]);

        return response()->json([
            'success' => true,
            'coupon' => $coupon->code,
            'discount' => round($discount, 2),
            'final_total' => round($finalTotal, 2),
            'message' => 'Coupon applied successfully'
        ]);
    }
}
