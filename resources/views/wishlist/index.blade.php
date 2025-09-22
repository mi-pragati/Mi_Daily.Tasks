@extends('layouts.customer')
@section('title', 'My Wishlist')

@section('content')
@php
  $wishlistIds = collect(session('wishlist.items', []))->map(fn($v)=>(int)$v)->all();
  $cartItems = session('cart.items', []); // Get current cart items
@endphp

<div class="container py-4">
  <h1 class="h3 mb-3">My Wishlist</h1>

  @if($products->isEmpty())
    <div class="alert alert-info">Your wishlist is empty.</div>
  @else
    <div class="row g-3">
      @foreach($products as $p)
        @php
          $img = $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/600x400';
          $alreadyInCart = isset($cartItems[$p->id]);
        @endphp

        <div class="col-6 col-md-4 col-lg-3">
          <div class="card h-100 border-0 shadow-sm">
            <a href="{{ route('products.show', $p->slug) }}" class="text-decoration-none text-dark">
              <img src="{{ $img }}" class="card-img-top" alt="{{ $p->name }}" style="aspect-ratio:4/3;object-fit:cover;">
              <div class="card-body">
                <div class="small text-muted mb-1">{{ optional($p->category)->name ?? '—' }}</div>
                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($p->name, 48) }}</div>
                <div class="mt-1">₹{{ number_format($p->price, 2) }}</div>
              </div>
            </a>

            <div class="card-footer bg-white border-0 pt-0 pb-3 d-flex gap-2">
  {{-- Add to cart --}}
  <form method="POST" action="{{ route('cart.store') }}" class="w-100">
    @csrf
    <input type="hidden" name="product_id" value="{{ $p->id }}">
    
    @if($alreadyInCart)
      <button class="btn btn-sm btn-secondary w-100" disabled>Already in Cart</button>
    @else
      <button class="btn btn-sm btn-primary w-100">Add to Cart</button>
    @endif
  </form>

  {{-- Remove from wishlist --}}
  <form method="POST" action="{{ route('wishlist.destroy', $p->id) }}">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">Remove</button>
  </form>
</div>

          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
