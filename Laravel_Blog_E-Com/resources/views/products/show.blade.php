@extends('lay_app')
@section('title', $product->title ?? $product->name)

@push('styles')
<style>
  /* wishlist heart styles (same as index) */
  .js-wishlist-toggle { border-radius: 10px; }
  .js-wishlist-toggle .wish-heart path{
    fill: transparent;
    stroke: #dc3545;
    stroke-width: 2;
    transition: fill .15s ease, transform .15s ease;
  }
  .js-wishlist-toggle.active .wish-heart path{ fill:#dc3545; stroke:#dc3545; }
  .js-wishlist-toggle:active .wish-heart{ transform: scale(0.95); }
</style>
@endpush

@section('content')
@php
  $wishlistIds = collect(session('wishlist.items', []))->map(fn($v)=>(int)$v)->all();
@endphp

<div class="container py-4">

  {{-- Breadcrumb-ish header --}}
  <nav class="mb-3">
    <a href="{{ route('products.index') }}">Shop</a>
    @if($product->category)
      &nbsp;/&nbsp;
      <a href="{{ route('products.filter', $product->category->slug) }}">{{ $product->category->name }}</a>
    @endif
    &nbsp;/&nbsp; <span class="text-muted">{{ $product->title ?? $product->name }}</span>
  </nav>

  {{-- Product main --}}
  <div class="row g-4">
    <div class="col-md-6">
      {{-- Use your accessor if present; else fallback via $imageUrl --}}
      <img src="{{ $product->image_url ?? $imageUrl }}"
           alt="{{ $product->title ?? $product->name }}"
           class="img-fluid rounded"
           style="width:100%; object-fit:cover;">
    </div>

    <div class="col-md-6">
      <h1 class="h3">{{ $product->title ?? $product->name }}</h1>
      @if($product->category)
        <div class="mb-1 text-muted">
          in <a href="{{ route('products.filter', $product->category->slug) }}">{{ $product->category->name }}</a>
        </div>
      @endif

      <div class="fs-4 fw-semibold my-2">₹{{ number_format((float) $product->price, 2) }}</div>

      <div class="mb-3">
        @if(($product->stock ?? 0) > 0)
          <span class="badge bg-success">In stock ({{ $product->stock }})</span>
        @else
          <span class="badge bg-secondary">Out of stock</span>
        @endif
      </div>

      <form method="POST" action="{{ route('cart.store') }}" class="d-flex align-items-center gap-2 mb-4">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="number" name="qty" value="1" min="1" class="form-control" style="max-width:120px"
               {{ ($product->stock ?? 0) < 1 ? 'disabled' : '' }}>
        <button class="btn btn-primary" {{ ($product->stock ?? 0) < 1 ? 'disabled' : '' }}>Add to Cart</button>

        {{-- wishlist heart for this product --}}
        @php $isActive = in_array((int)$product->id, $wishlistIds, true); @endphp
        <button
          type="button"
          class="btn btn-light js-wishlist-toggle {{ $isActive ? 'active' : '' }}"
          data-product-id="{{ $product->id }}"
          aria-pressed="{{ $isActive ? 'true' : 'false' }}"
          title="Add to wishlist"
        >
          <svg width="22" height="22" viewBox="0 0 24 24" class="wish-heart" aria-hidden="true">
            <path d="M12.001 20.727s-7.2-4.373-9.6-8.182C.733 10.072 2.23 6.545 5.4 6.545c2.127 0 3.164 1.309 3.6 2.182.436-.873 1.473-2.182 3.6-2.182 3.17 0 4.666 3.527 3 6-2.4 3.809-9.6 8.182-9.6 8.182z"/>
          </svg>
        </button>
      </form>

      @if(!empty($product->description))
        <h5>Description</h5>
        <p class="mb-0">{{ $product->description }}</p>
      @endif
    </div>
  </div>

  {{-- Related products --}}
  @if(isset($related) && $related->count())
    <hr class="my-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h2 class="h5 m-0">Related products</h2>
      @if($product->category)
        <a href="{{ route('products.filter', $product->category->slug) }}" class="small">See more</a>
      @endif
    </div>

    <div class="row g-3">
      @foreach($related as $rp)
        @php
          $img = $rp->image ? asset('storage/'.$rp->image) : 'https://via.placeholder.com/600x400';
          $active = in_array((int)$rp->id, $wishlistIds, true);
        @endphp
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
          <div class="card h-100 border-0 shadow-sm">
            <div class="position-relative">
              <a href="{{ route('products.show', $rp->slug) }}" class="d-block">
                <img src="{{ $img }}" alt="{{ $rp->name }}" class="card-img-top" style="aspect-ratio:4/3;object-fit:cover;">
              </a>
              <button
                class="btn btn-light shadow position-absolute top-0 end-0 m-2 js-wishlist-toggle {{ $active ? 'active' : '' }}"
                data-product-id="{{ $rp->id }}"
                aria-pressed="{{ $active ? 'true' : 'false' }}"
                type="button"
                title="Add to wishlist"
              >
                <svg width="22" height="22" viewBox="0 0 24 24" class="wish-heart" aria-hidden="true">
                  <path d="M12.001 20.727s-7.2-4.373-9.6-8.182C.733 10.072 2.23 6.545 5.4 6.545c2.127 0 3.164 1.309 3.6 2.182.436-.873 1.473-2.182 3.6-2.182 3.17 0 4.666 3.527 3 6-2.4 3.809-9.6 8.182-9.6 8.182z"/>
                </svg>
              </button>
            </div>
            <div class="card-body">
              <div class="small text-muted mb-1">{{ optional($rp->category)->name ?? '—' }}</div>
              <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($rp->name, 40) }}</div>
              <div class="mt-1">₹{{ number_format($rp->price, 2) }}</div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
  // Use the same AJAX toggle pattern you already have
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-wishlist-toggle');
    if (!btn) return;

    e.preventDefault();

    const productId = btn.getAttribute('data-product-id');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
      const res = await fetch(@json(route('wishlist.toggle')), {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
      });

      if (!res.ok) {
        console.error('Wishlist error', res.status, await res.text());
        alert('Could not update wishlist. Please try again.');
        return;
      }

      const data = await res.json();
      if (!data.ok) throw new Error(data.message || 'Wishlist failed');

      btn.classList.toggle('active', data.in_wishlist === true);
      btn.setAttribute('aria-pressed', data.in_wishlist ? 'true' : 'false');

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
