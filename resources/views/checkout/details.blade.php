@extends('lay_app')

@section('title', 'Enter Delivery Details')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">Delivery Details</h1>

  <form method="POST" action="{{ route('checkout.placeOrder') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" required>
    </div>

    <button type="submit" class="btn btn-success">Place Order</button>
    <a href="{{ route('customer.orders.confirmation') }}" class="btn btn-outline-secondary ms-2">Back to Summary</a>
  </form>
</div>
@endsection
