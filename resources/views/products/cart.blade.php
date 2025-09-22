@extends('lay_app')

@section('content')
    <div class="container my-5">
        <h3>Your Cart</h3>
        <div class="row">
            @foreach(session('cart', []) as $product)
                <div class="col-md-4">
                    <div class="card">
                        <img src="{{ asset('storage/products/'.$product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">₹{{ number_format($product->price * 75, 2) }}</p>
                            <!-- You can add an option to remove from cart -->
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <a href="{{ route('checkout') }}" class="btn btn-success mt-3">Proceed to Checkout</a>
    </div>
@endsection
