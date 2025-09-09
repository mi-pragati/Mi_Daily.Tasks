<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// Added models for DB-backed wishlist & cart
use App\Models\WishlistItem;
use App\Models\Cart;
use App\Models\CartItem;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Merge guest session data into DB after login
        $this->mergeSessionWishlistToDb();
        $this->mergeSessionCartToDb();

        $role = Auth::user()->role ?? null;

        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'editor'   => redirect()->route('editor.home'),
            'customer' => redirect()->route('customer.dashboard'),
            default    => redirect()->route('homi'),
        };
    }

    /**
     * (You pasted this here) Cart index fallback.
     * Added helpers below so this still works if left in this controller.
     */
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
        $items  = $this->sessionCart();
        $totals = $this->totals($items);
        return view('cart.index', compact('items', 'totals'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Clear any guest session copies so badges don't resurrect
        $request->session()->forget([
            'cart', 'cart.items',
            'wishlist', 'wishlist.items', 'wishlist.count',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('homi');
    }

    /* =========================
       Helpers for merge/login
       ========================= */

    protected function mergeSessionWishlistToDb(): void
    {
        if (!Auth::check()) return;

        $ids = collect(session('wishlist.items', []))
            ->map(fn ($v) => (int) (is_array($v) ? ($v['id'] ?? $v['product_id'] ?? $v) : $v))
            ->filter()
            ->unique();

        foreach ($ids as $pid) {
            WishlistItem::firstOrCreate([
                'user_id'    => Auth::id(),
                'product_id' => $pid,
            ]);
        }

        // Clear session wishlist after merge
        session()->forget(['wishlist.items', 'wishlist.count']);
    }

    protected function mergeSessionCartToDb(): void
    {
        if (!Auth::check()) return;

        $items = collect(session('cart.items', []));
        if ($items->isEmpty()) return;

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        // Upsert each line into DB cart
        foreach ($items as $pid => $row) {
            $productId = is_numeric($pid) ? (int) $pid : (int) ($row['id'] ?? $row['product_id'] ?? 0);
            if (!$productId) continue;

            $qty   = (int) ($row['qty']   ?? 1);
            $price = (float)($row['price'] ?? 0);

            $cart->items()->updateOrCreate(
                ['product_id' => $productId],
                ['qty' => $qty, 'price' => $price]
            );
        }

        // Clear session cart after merge
        session()->forget(['cart.items', 'cart']);
    }

    /* =========================
       Helpers used by index()
       (since you placed index here)
       ========================= */

    protected function sessionCart(): array
    {
        return session()->get('cart.items', []);
    }

    protected function totals(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $row) {
            $subtotal += ((float)$row['price']) * ((int)$row['qty']);
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
}
