@extends('layouts.customer')

@section('title', 'Return Order')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Return Order #{{ $order->id }}</h3>

    <div class="card shadow-sm p-3">
        <form action="{{ route('customer.orders.return.submit', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Customer Details --}}
            <div class="mb-3">
                <label>Name</label>
                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
            </div>
            <div class="mb-3">
                <label>Phone</label>
                <input type="text" class="form-control" value="{{ $order->phone }}" readonly>
            </div>
            <div class="mb-3">
                <label>Address</label>
                <textarea class="form-control" readonly>{{ $order->address }}</textarea>
            </div>

            {{-- Ordered Products --}}
            <div class="mb-3">
                <label>Ordered Products</label>
                <ul class="list-group">
                    @foreach($order->orderItems as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <img src="{{ $item->product->image_url ?? asset('storage/'.$item->image) }}" 
                                     alt="" width="50" class="me-2">
                                {{ $item->product->name ?? $item->title }} (x{{ $item->qty }})
                            </div>
                            <span>₹{{ number_format($item->price * $item->qty, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-2"><strong>Total: ₹{{ number_format($order->total, 2) }}</strong></p>
            </div>

            {{-- Message --}}
            <div class="mb-3">
                <label>Reason / Message*</label>
                <textarea name="message" class="form-control" required>{{ old('message') }}</textarea>
            </div>

            {{-- Upload Image --}}
            <div class="mb-3">
                <label>Upload Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-danger">Submit Return</button>
        </form>
    </div>
</div>

{{-- Success popup using session --}}
@if(session('success'))
<script>
    alert("{{ session('success') }}");
    window.location.href = "{{ route('customer.orders.index') }}";
</script>
@endif
@endsection
