@extends('lay_app')

@section('title', 'Order Confirmation')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="text-success">🎉 Order Confirmed!</h1>
        <p class="lead">Thank you for your purchase. Your order has been successfully placed.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title">Order Details</h4>
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y h:i A') }}</p>
            <p><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-success text-uppercase">{{ $order->status }}</span>
            </p>
            
        </div>
    </div>

    <div class="mt-4">
        <h5>Ordered Items:</h5>
        <ul class="list-group">
            @foreach($order->orderItems as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $item->product->title }}
                    <span>{{ $item->qty }} x ₹{{ number_format($item->price, 2) }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            Continue Shopping
        </a>
    </div>
</div>
@endsection
