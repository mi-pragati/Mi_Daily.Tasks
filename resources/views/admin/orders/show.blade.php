@extends('admin.layouts.app')

@section('title', 'Order #'.$order->id)

@section('content')
<div class="container py-4">

    {{-- Order Number in small font --}}
    <h1 style="font-size: 1.25rem;">Order #{{ $order->id }}</h1>
    <hr>

    {{-- Order Date, Status, Total --}}
    <p><strong>Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
    <p><strong>Status:</strong> {{ $order->status ?? 'Pending' }}</p>
    <p><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>

    {{-- Shipping Details with underline only for the title --}}
    <h3 style="font-size: 1.1rem; display: inline-block; border-bottom: 1px solid #000;">
        Shipping Details
    </h3>
    <div style="font-size: 0.9rem; margin-top: 10px;">
        <p><strong>Name:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Phone:</strong> {{ $order->phone }}</p>
        <p><strong>Address:</strong> {{ $order->address }}</p>
    </div>

    {{-- Items --}}
    <h3 style="font-size: 1.1rem; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 20px;">
        Items
    </h3>
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                <tr>
                    <td>
                        @php
                            $img = $item->product->image ?? null;

                            if ($img) {
                                if (Str::startsWith($img, ['http://', 'https://'])) {
                                    $imageUrl = $img;
                                } else {
                                    $imageUrl = asset('storage/' . $img);
                                }
                            } else {
                                $imageUrl = 'https://picsum.photos/seed/fallback/60/60';
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $item->product->title ?? 'Product' }}" 
                             width="60" height="60" style="object-fit: cover;">
                    </td>
                    <td>{{ $item->product->title ?? 'N/A' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>₹{{ number_format($item->price, 2) }}</td>
                    <td>₹{{ number_format($item->price * $item->qty, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($order->returns)
    @php
        $return = $order->returns instanceof \Illuminate\Database\Eloquent\Collection
                    ? $order->returns->last()
                    : $order->returns;
    @endphp

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-warning text-dark">
            Return Details
        </div>
        <div class="card-body">
            <p><strong>Customer Message:</strong> {{ $return->message ?? '-' }}</p>

            @if($return->image)
                <p><strong>Return Image:</strong></p>
                <img src="{{ asset('storage/' . $return->image) }}" alt="Return Image" class="img-fluid mb-3" style="max-width: 300px;">
            @endif
            <br>
            {{-- Dropdown to Accept/Decline --}}
            <form action="{{ route('admin.orders.updateReturn', $order->id) }}" method="POST" class="d-inline-block">
                @csrf
                @method('PATCH')
                <div class="mb-2">
                    <select name="status" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
                        <option value="Return Pending" {{ $return->status == 'Return Pending' ? 'selected' : '' }}>Return Pending</option>
                        <option value="Returned" {{ $return->status == 'Returned' ? 'selected' : '' }}>Accept Return</option>
                        <option value="Rejected" {{ $return->status == 'Rejected' ? 'selected' : '' }}>Decline Return</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
@endif

    {{-- Back to Orders Button --}}
    <div class="mt-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            &larr; Back to Orders
        </a>
    </div>
</div>
@endsection
