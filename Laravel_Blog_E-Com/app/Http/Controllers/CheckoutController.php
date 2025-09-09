<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $items  = session('cart.items', []);
        $total  = collect($items)->reduce(fn($c,$r) => $c + ($r['price'] * $r['qty']), 0);

        return view('checkout.index', compact('items','total'));
    }
}
