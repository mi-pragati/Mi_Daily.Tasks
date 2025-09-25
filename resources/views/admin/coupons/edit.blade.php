@extends('admin.layouts.app')

@section('title', 'Edit Coupon')

@section('content')
<div class="container py-4">
    <h2>Edit Coupon</h2>

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        {{-- Code --}}
        <div class="mb-3">
            <label for="code" class="form-label">Coupon Code</label>
            <input type="text" name="code" id="code" class="form-control"
                   value="{{ old('code', $coupon->code) }}" required>
        </div>

        {{-- Type --}}
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="percent" {{ $coupon->type === 'percent' ? 'selected' : '' }}>Percentage</option>
            </select>
        </div>

        {{-- Value --}}
        <div class="mb-3">
            <label for="value" class="form-label">Value</label>
            <input type="number" name="value" id="value" class="form-control"
                   value="{{ old('value', $coupon->value) }}" required>
        </div>

        {{-- Expiry --}}
        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control"
                   value="{{ old('expiry_date', $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d') : '') }}">
        </div>

        <div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-success px-4 py-2">
        Update Coupon
    </button>

    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary px-4 py-2">
        Cancel
    </a>
</div>

</div>
@endsection
