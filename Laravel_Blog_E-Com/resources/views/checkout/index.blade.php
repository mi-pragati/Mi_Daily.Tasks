@extends('lay_app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">Checkout</h1>

  @if (empty($items))
    <div class="alert alert-info">Your cart is empty.</div>
    <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
    @return
  @endif

  <div class="card mb-3">
    <div class="card-header">Order Summary</div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        @foreach($items as $row)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ $row['title'] }} × {{ $row['qty'] }}</span>
            <span>₹{{ number_format($row['price'] * $row['qty'], 2) }}</span>
          </li>
        @endforeach
        <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
          <span>Total</span>
          <span>₹{{ number_format($total, 2) }}</span>
        </li>
      </ul>
    </div>
  </div>

  {{-- Placeholder checkout action --}}
  <form method="POST" action="#">
    @csrf
    <button class="btn btn-success">Place Order (demo)</button>
    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary ms-2">Back to Cart</a>
  </form>
</div>
@endsection
