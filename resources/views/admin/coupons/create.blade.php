@extends('admin.layouts.app')

@section('title', 'Create Coupon')

@section('content')
<div class="container py-4">
    <h2>Create Coupon</h2>

    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Coupon Code</label>
            <input type="text" name="code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
                <option value="fixed">Fixed (₹)</option>
                <option value="percent">Percent (%)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Value</label>
            <input type="number" name="value" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Expiry Date (optional)</label>
            <input type="date" name="expiry_date" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Create</button>
    </form>
</div>
@endsection
