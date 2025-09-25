@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
<div class="container py-4">
    <h2>Coupons</h2>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mb-3">+ New Coupon</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($coupons as $coupon)
            <tr>
                <td>{{ $coupon->code }}</td>
                <td>{{ ucfirst($coupon->type) }}</td>
                <td>
                    @if($coupon->type == 'fixed')
                        ₹{{ $coupon->value }}
                    @else
                        {{ $coupon->value }}%
                    @endif
                </td>
                <td>{{ $coupon->expiry_date ?? 'No limit' }}</td>

               <td class="d-flex gap-2">
    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning">
        Edit
    </a>

    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this coupon?')">Delete</button>
    </form>
</td>

            </tr>
        @empty
            <tr><td colspan="5" class="text-center">No coupons yet</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
