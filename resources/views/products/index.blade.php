@extends('lay_app')

@section('title', isset($activeCategory) ? 'Shop: '.$activeCategory->name : 'Shop')

@push('styles')
<style>
  .js-wishlist-toggle { border-radius: 10px; }
  .js-wishlist-toggle .wish-heart path{
    fill: transparent;
    stroke: #dc3545;
    stroke-width: 2;
    transition: fill .15s ease, transform .15s ease;
  }
  .js-wishlist-toggle.active .wish-heart path{
    fill: #dc3545;
    stroke: #dc3545;
  }
  .js-wishlist-toggle:active .wish-heart { transform: scale(0.95); }
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Str;
  // grab current wishlist ids from session so we can pre-highlight the heart
  $wishlistIds = collect(session('wishlist.items', []))->map(fn($v)=>(int)$v)->all();
@endphp

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">
      @if(isset($activeCategory))
        Shop: {{ $activeCategory->name }}
      @else
        Shop All Products
      @endif
    </h1>

    <form method="GET"
          action="{{ isset($activeCategory) ? route('products.filter', $activeCategory->slug) : route('products.index') }}"
          class="d-flex" role="search" style="gap:.5rem;">
      <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Search products">
      <button class="btn btn-outline-primary">Search</button>
    </form>
  </div>

  {{-- Shop by Category --}}
  <div class="mb-4">
    <div class="d-flex flex-wrap" style="gap:.5rem;">
      <a href="{{ route('products.index') }}"
         class="btn btn-sm {{ !isset($activeCategory) ? 'btn-primary' : 'btn-outline-primary' }}">
        All
      </a>
      @foreach($categories as $cat)
        <a href="{{ route('products.filter', $cat->slug) }}"
           class="btn btn-sm {{ (isset($activeCategory) && $activeCategory->id === $cat->id) ? 'btn-primary' : 'btn-outline-primary' }}">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>
  </div>

  @if($products->count())
    <div class="row g-3">
      @foreach($products as $p)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card h-100 border-0 shadow-sm">
            <div class="position-relative">
              <a href="{{ route('products.show', $p->slug) }}" class="text-decoration-none text-dark d-block">
                <img src="{{ $p->image_url }}" class="card-img-top" alt="{{ $p->title }}"
                     style="aspect-ratio: 4/3; object-fit: cover;">
              </a>

              {{-- Heart button in the image corner --}}
              <button
                class="btn btn-light shadow position-absolute top-0 end-0 m-2 js-wishlist-toggle {{ in_array((int)$p->id, $wishlistIds, true) ? 'active' : '' }}"
                data-product-id="{{ $p->id }}"
                aria-pressed="{{ in_array((int)$p->id, $wishlistIds, true) ? 'true' : 'false' }}"
                aria-label="Toggle wishlist"
                type="button"
                title="Add to wishlist"
              >
                <svg width="22" height="22" viewBox="0 0 24 24" class="wish-heart" aria-hidden="true">
                  <path d="M12.001 20.727s-7.2-4.373-9.6-8.182C.733 10.072 2.23 6.545 5.4 6.545c2.127 0 3.164 1.309 3.6 2.182.436-.873 1.473-2.182 3.6-2.182 3.17 0 4.666 3.527 3 6-2.4 3.809-9.6 8.182-9.6 8.182z"/>
                </svg>
              </button>
            </div>

            <div class="card-body">
              <h5 class="card-title h6 mb-1">{{ Str::limit($p->title, 42) }}</h5>
              <div class="text-muted small mb-2">{{ optional($p->category)->name ?? '—' }}</div>
              <div class="fw-semibold">₹{{ number_format($p->price, 2) }}</div>
            </div>

            <div class="card-footer bg-white border-0 pt-0 pb-3">
              <form method="POST" action="{{ route('cart.store') }}" class="d-grid">
                @csrf
                <input type="hidden" name="product_id" value="{{ $p->id }}">
                <button class="btn btn-outline-primary btn-sm" {{ $p->stock < 1 ? 'disabled' : '' }}>
                  {{ $p->stock < 1 ? 'Out of Stock' : 'Add to Cart' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $products->links() }}
    </div>
  @else
    <div class="alert alert-info">No products found.</div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  // AJAX toggle for wishlist
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-wishlist-toggle');
    if (!btn) return;

    e.preventDefault();

    const productId = btn.getAttribute('data-product-id');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
      const res = await fetch(@json(route('wishlist.toggle')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
      });

      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error(data.message || 'Wishlist failed');

      // Toggle the heart
      btn.classList.toggle('active', data.in_wishlist === true);
      btn.setAttribute('aria-pressed', data.in_wishlist ? 'true' : 'false');

      // Update navbar badge
      const badge = document.getElementById('wishlist-badge');
      if (badge) {
        if (data.count > 0) {
          badge.classList.remove('d-none');
          badge.textContent = data.count;
        } else {
          badge.classList.add('d-none');
          badge.textContent = '0';
        }
      }
    } catch (err) {
      console.error(err);
      alert('Could not update wishlist. Please try again.');
    }
  });
</script>
@endpush
