<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
      protected function sessionCart(): array
    {
        return session()->get('cart.items', []);
    }

    protected function putSessionCart(array $items): void
    {
        session()->put('cart.items', $items);
    }

    protected function totals(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $row) {
            $subtotal += $row['price'] * $row['qty'];
        }
        return [
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ];
    }

    protected function userCart(): ?Cart
    {
        if (!Auth::check()) return null;
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }

    protected function syncToDb(array $items): void
    {
        $cart = $this->userCart();
        if (!$cart) return;

        // Sync rows
        $existing = $cart->items()->get()->keyBy('product_id');

        // Upsert from session
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


    public function index(Request $request)
{
    if (Auth::check()) {
        $cart = $this->userCart();
        $rows = $cart ? $cart->items()->with('product')->get() : collect();

        $items = [];
        foreach ($rows as $row) {
            $p = $row->product;
            $items[$row->product_id] = [
                'id'    => $row->product_id,
                'title' => $p->title ?? $p->name ?? 'Product',
                'price' => (float) $row->price,
                'qty'   => (int) $row->qty,
                'image' => ($p->image_url ?? $p->image ?? $p->thumbnail ?? null)
                    ? ($p->image_url ?? (isset($p->image) ? asset('storage/'.$p->image) : $p->thumbnail))
                    : null,
            ];
        }

        $totals = $this->totals($items);
        return view('cart.index', compact('items', 'totals'));
    }

    // Guest fallback: session
    $items = $this->sessionCart();
    $totals = $this->totals($items);
    return view('cart.index', compact('items', 'totals'));
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty'        => ['nullable','integer','min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qty = max(1, (int)($data['qty'] ?? 1));

        // Stock guard
        if ($product->stock < $qty) {
            return back()->withErrors(['qty' => 'Only '.$product->stock.' left in stock.']);
        }

        $items = $this->sessionCart();
        if (isset($items[$product->id])) {
            $newQty = $items[$product->id]['qty'] + $qty;
            if ($newQty > $product->stock) {
                return back()->withErrors(['qty' => 'Max available: '.$product->stock]);
            }
            $items[$product->id]['qty'] = $newQty;
        } else {
            $items[$product->id] = [
                'id'    => $product->id,
                'title' => $product->title ?? $product->name ?? 'Product',
                'price' => (float)$product->price,
                'qty'   => $qty,
                'image' => method_exists($product, 'getImageUrlAttribute')
                            ? $product->image_url
                            : ($product->image ? asset('storage/'.$product->image) : null),
            ];
        }

        $this->putSessionCart($items);
        $this->syncToDb($items);

        return redirect()->route('cart.index')->with('status', 'Added to cart.');
    }

    public function update(Request $request, int $id)
{
    $data = $request->validate([
        'qty' => ['required','integer','min:1'],
    ]);

    $product = Product::findOrFail($id);

    if ($product->stock < $data['qty']) {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'error' => 'Only '.$product->stock.' left in stock.'], 422);
        }
        return back()->withErrors(['qty' => 'Only '.$product->stock.' left in stock.']);
    }

    $items = $this->sessionCart();

    if (!isset($items[$id])) {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'error' => 'Item not in cart'], 404);
        }
        return back()->withErrors(['cart' => 'Item not in cart.']);
    }

    $items[$id]['qty'] = $data['qty'];
    $this->putSessionCart($items);
    $this->syncToDb($items);

    // recompute totals
    $totals    = $this->totals($items);
    $lineTotal = $items[$id]['price'] * $items[$id]['qty'];

    if ($request->expectsJson()) {
        return response()->json([
            'ok'         => true,
            'qty'        => $items[$id]['qty'],
            'line_total' => $lineTotal,
            'subtotal'   => $totals['subtotal'],
            'total'      => $totals['total'],
        ]);
    }

    return back()->with('status', 'Quantity updated.');
}
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


    public function clear()
    {
        $this->putSessionCart([]);
        $this->syncToDb([]);
        return back()->with('status', 'Cart cleared.');
    }

    public function legacyAdd(Product $product)
    {
        $request = request();
        $request->merge(['product_id' => $product->id, 'qty' => 1]);
        return $this->store($request);
    }
}
