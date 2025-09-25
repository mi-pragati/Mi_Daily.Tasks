<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function edit($id)
{
    $coupon = Coupon::findOrFail($id);
    return view('admin.coupons.edit', compact('coupon'));
}

public function update(Request $request, $id)
{
    $coupon = Coupon::findOrFail($id);

    $request->validate([
        'code' => 'required|string|unique:coupons,code,' . $coupon->id,
        'type' => 'required|in:fixed,percent',
        'value' => 'required|numeric|min:1',
        'expiry_date' => 'nullable|date|after_or_equal:today',
    ]);

    $coupon->update($request->only('code', 'type', 'value', 'expiry_date'));

    return redirect()->route('admin.coupons.index')
                     ->with('success', 'Coupon updated successfully.');
}


    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        Coupon::create($request->all());

        return redirect()->route('admin.coupons.index')
                         ->with('success', 'Coupon created successfully!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
                         ->with('success', 'Coupon deleted successfully!');
    }
}
