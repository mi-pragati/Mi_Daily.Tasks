@extends('layouts.customer')

@section('title', 'Order #'.$order->id)

@section('content')
<div class="container py-4">

    {{-- Order Number --}}
    <h1 style="font-size: 1.25rem;">Order #{{ $order->id }}</h1>
    <hr>

    {{-- Order Info --}}
    <p><strong>Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
    <p><strong>Status:</strong> {{ $order->status ?? 'Pending' }}</p>
    <p><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>

    {{-- Reorder / Return Status --}}
@php
    $reorderedOrderId = session('reordered_order_id');
@endphp

@if($reorderedOrderId && $reorderedOrderId == $order->id)
    <span class="btn btn-sm btn-outline-primary">Re-Ordered</span>
@endif

{{-- Show return status if exists --}}
@if($order->returns->isNotEmpty())
    @php $returnStatus = $order->returns->last()->status; @endphp
    <span class="btn btn-sm 
        @if($returnStatus === 'Pending') btn-outline-warning text-dark
        @elseif($returnStatus === 'Accepted') btn-outline-success
        @elseif($returnStatus === 'Declined') btn-outline-danger
        @endif" disabled>
        @if($returnStatus === 'Accepted')
            Returned
        @else
            Return {{ $returnStatus }}
        @endif
    </span>
@endif
<br>
<br>
    {{-- Shipping Details --}}
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
                @php
                    $product = $item->product; // may be null
                    $title = $product->title ?? $item->title ?? 'N/A';
                    $img = $product->image ?? $item->image ?? null;

                    if ($img) {
                        $imageUrl = Str::startsWith($img, ['http://','https://']) 
                                    ? $img 
                                    : asset('storage/'.$img);
                    } else {
                        $imageUrl = 'https://picsum.photos/seed/fallback/60/60';
                    }

                    $price = $item->price ?? ($product->price ?? 0);
                    $subtotal = $price * $item->qty;
                @endphp
                <tr>
                    <td>
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $title }}" 
                             width="60" height="60" style="object-fit: cover;">
                    </td>
                    <td>{{ $title }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>₹{{ number_format($price, 2) }}</td>
                    <td>₹{{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Back Button --}}
    <div class="mt-4">
        <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">
            &larr; Back to Orders
        </a>
    </div>
</div>
@endsection
@php
    session()->forget('reordered_order_id');
@endphp
