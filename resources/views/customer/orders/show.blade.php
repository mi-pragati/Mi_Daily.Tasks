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

    {{-- Total Amount --}}
    <p><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>

    {{-- Reorder / Return Status --}}
    @php $reorderedOrderId = session('reordered_order_id'); @endphp
    @if($reorderedOrderId && $reorderedOrderId == $order->id)
        <span class="btn btn-sm btn-outline-primary">Re-Ordered</span>
    @endif

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
    <br><br>

    {{-- Shipping Details --}}
<h3 style="font-size: 1.1rem; 
           display: inline-block; 
           border-bottom: 2px solid #000; 
           padding-bottom: 5px; 
           margin-top: 15px; 
           margin-bottom: 15px;">
    Shipping Details
</h3>    
<br>
    <div style="font-size: 0.9rem; margin-top: 10px;">
        <p><strong>Name:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Phone:</strong> {{ $order->phone }}</p>
        <p><strong>Address:</strong> {{ $order->address }}</p>
    </div>

    {{-- Items Table --}}
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
                    $product = $item->product;
                    $title = $product->title ?? $item->title ?? 'N/A';
                    $img = $product->image ?? $item->image ?? null;

                    $imageUrl = $img 
                        ? (Str::startsWith($img, ['http://','https://']) ? $img : asset('storage/'.$img))
                        : 'https://picsum.photos/seed/fallback/60/60';

                    $price = $item->price ?? ($product->price ?? 0);
                    $subtotal = $item->subtotal ?? ($price * $item->qty);
                @endphp
                <tr>
                    <td>
                        <img src="{{ $imageUrl }}" alt="{{ $title }}" width="60" height="60" style="object-fit: cover;">
                    </td>
                    <td>{{ $title }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>₹{{ number_format($price, 2) }}</td>
                    <td>₹{{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Order Summary --}}
    <h3 style="font-size: 1.1rem; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 20px;">
        Order Summary
    </h3>
    <table class="table table-borderless" style="max-width: 400px;">
        <tbody>
            <tr>
                <td>Subtotal:</td>
                <td>₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Discount 
                    @if($order->coupon_code)
                        ({{ $order->coupon_code }})
                    @endif
                    :
                </td>
                <td>₹{{ number_format($order->discount, 2) }}</td>
            </tr>
            <tr>
                <th>Final Total:</th>
                <th>₹{{ number_format($order->final_total, 2) }}</th>
            </tr>
        </tbody>
    </table>

    {{-- Back Button --}}
    <div class="mt-4">
        <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">
            &larr; Back to Orders
        </a>
    </div>

</div>

@php
    session()->forget('reordered_order_id');
@endphp
@endsection
