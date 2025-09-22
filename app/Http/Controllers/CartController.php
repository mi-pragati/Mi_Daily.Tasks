<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get the current user's cart items (session or DB).
     */
    protected function sessionCart(): array
    {
        if (Auth::check()) {
            $cart = $this->userCart();
            if (!$cart) return [];

            $rows = $cart->items()->with('product')->get();
            $items = [];
            foreach ($rows as $row) {
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

        // Guest fallback
        return session()->get('cart.items', []);
    }

    /**
     * Get or create cart for authenticated user.
     */
    protected function userCart(): ?Cart
    {
        return Auth::check() ? Cart::firstOrCreate(['user_id' => Auth::id()]) : null;
    }

    /**
     * Store items in session.
     */
    protected function putSessionCart(array $items): void
    {
        session(['cart.items' => $items]);
    }

    /**
     * Sync session cart to database.
     */
    protected function syncToDb(array $items): void
    {
        $cart = $this->userCart();
        if (!$cart) return;

        $existing = $cart->items()->get()->keyBy('product_id');

        foreach ($items as $pid => $row) {
            $cart->items()->updateOrCreate(
                ['product_id' => $pid],
                ['qty' => $row['qty'], 'price' => $row['price']]
            );
            unset($existing[$pid]);
        }

        foreach ($existing as $toDelete) {
            $toDelete->delete();
        }
    }

    /**
     * Compute totals.
     */
    protected function totals(array $items): array
    {
        $subtotal = collect($items)->reduce(fn($c, $r) => $c + ($r['price'] * $r['qty']), 0);
        return [
            'subtotal' => $subtotal,
            'total'    => $subtotal, // modify if you add tax/shipping
        ];
    }

    /**
     * Show cart.
     */
    public function index(Request $request)
    {
        $items = $this->sessionCart();
        $totals = $this->totals($items);

        return view('cart.index', compact('items', 'totals'));
    }

    /**
     * Add item to cart.
     */
    // 🚫 Do not reduce stock in store()
public function store(Request $request)
{
    $data = $request->validate([
        'product_id' => ['required', 'integer', 'exists:products,id'],
        'qty'        => ['nullable', 'integer', 'min:1'],
    ]);

    $product = Product::findOrFail($data['product_id']);
    $qty = max(1, (int)($data['qty'] ?? 1));

    // ✅ Just check availability but don't reduce stock here
    if ($product->stock < $qty) {
        return back()->withErrors(['qty' => 'Only ' . $product->stock . ' left in stock.']);
    }

    $items = $this->sessionCart();

    if (isset($items[$product->id])) {
        $items[$product->id]['qty'] += $qty;
    } else {
        $items[$product->id] = [
            'id'    => $product->id,
            'title' => $product->title ?? $product->name ?? 'Product',
            'price' => (float) $product->price,
            'qty'   => $qty,
            'image' => $product->image_url ?? ($product->image ? asset('storage/' . $product->image) : null),
        ];
    }

    $this->putSessionCart($items);
    $this->syncToDb($items);

    return redirect()->route('cart.index')->with('status', 'Added to cart.');
}

    /**
     * Update cart item qty.
     */
    // 🚫 Do not touch stock in update()
public function update(Request $request, int $id)
{
    $data = $request->validate([
        'qty' => ['required', 'integer', 'min:1'],
    ]);

    $product = Product::findOrFail($id);

    $items = $this->sessionCart();
    if (!isset($items[$id])) {
        return back()->withErrors(['cart' => 'Item not in cart.']);
    }

    $items[$id]['qty'] = $data['qty'];
    $this->putSessionCart($items);
    $this->syncToDb($items);

    $totals = $this->totals($items);
    $lineTotal = $items[$id]['price'] * $data['qty'];

    if ($request->ajax()) {
        return response()->json([
            'ok'        => true,
            'line_total'=> $lineTotal,
            'subtotal'  => $totals['subtotal'],
            'total'     => $totals['total'],
        ]);
    }

    return back()->with('status', 'Quantity updated.');
}

    /**
     * Remove item from cart.
     */
    // 🚫 Do not restore stock in destroy()
public function destroy(int $id)
{
    $items = $this->sessionCart();

    if (isset($items[$id])) {
        unset($items[$id]);
        $this->putSessionCart($items);
        $this->syncToDb($items);
    }

    return back()->with('status', 'Item removed.');
}
    /**
     * Clear entire cart and restore stock.
     */
    public function clear()
{
    $this->putSessionCart([]);
    $this->syncToDb([]);
    return back()->with('status', 'Cart cleared.');
}
    /**
     * Quick add (legacy route).
     */
    public function legacyAdd(Product $product)
    {
        request()->merge(['product_id' => $product->id, 'qty' => 1]);
        return $this->store(request());
    }
}
