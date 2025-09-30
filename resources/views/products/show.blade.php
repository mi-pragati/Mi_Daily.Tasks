@extends('lay_app')
@section('title', $product->title)

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

  /* Smooth horizontal scroll */
.overflow-auto::-webkit-scrollbar {
    height: 8px;
}
.overflow-auto::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,.2);
    border-radius: 4px;
}

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
    &nbsp;/&nbsp; <span class="text-muted">{{ $product->title}}</span>
  </nav>

  {{-- Product main --}}
  <div class="row g-4">
    <div class="col-md-6">
      {{-- Use your accessor if present; else fallback via $imageUrl --}}
      <img src="{{ $product->image_url ?? $imageUrl }}"
           alt="{{ $product->title}}"
           class="img-fluid rounded"
           style="width:100%; object-fit:cover;">
    </div>

    <div class="col-md-6">
      <h1 class="h3">{{ $product->title}}</h1>
      @if($product->category)
        <div class="mb-1 text-muted">
          in <a href="{{ route('products.filter', $product->category->slug) }}">{{ $product->category->name }}</a>
        </div>
      @endif

      <div class="fs-4 fw-semibold my-2">₹{{ number_format((float) $product->price, 2) }}</div>

      <div class="mb-3">
        @if($product->stock > 0)
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

      <hr class="my-4">

<h4>Reviews</h4>

{{-- Show average rating --}}
@php $avg = round($product->averageRating(), 1); @endphp
<div class="mb-2">
    <span class="fw-semibold">Average Rating:</span>
    <span class="text-warning">
        @for ($i = 1; $i <= 5; $i++)
            <i class="fa{{ $i <= $avg ? 's' : 'r' }} fa-star"></i>
        @endfor
    </span>
    <small>({{ $avg }}/5)</small>
</div>

{{-- Review Form --}}
@auth
<form id="review-form" action="{{ route('reviews.store', $product) }}" method="POST" class="mb-3">
    @csrf
    <div class="mb-2">
        <label for="rating" class="d-block">Your Rating</label>
        <div id="star-rating" class="d-flex gap-1" style="font-size: 1.5rem; cursor: pointer;">
            @for ($i = 1; $i <= 5; $i++)
                <span class="star" data-value="{{ $i }}">&#9733;</span>
            @endfor
        </div>
        <input type="hidden" name="rating" id="rating" required>
    </div>

    <div class="mb-2">
        <textarea name="comment" class="form-control" placeholder="Write your review..." rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
</form>
@endauth

{{-- Reviews List --}}
<div id="reviews-list">
    @foreach ($product->reviews()->latest()->get() as $review)
        <div class="mb-3 border-bottom pb-2 review" id="review-{{ $review->id }}">
            <strong>{{ $review->user->name }}</strong>
        <span class="text-warning ms-2">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
            @endfor
        </span>
            <p>{{ $review->comment }}</p>

            @if (Auth::check() && $review->user_id === auth()->id())
                <form action="{{ route('reviews.destroy', [$review->product, $review]) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>

            @endif
        </div>
    @endforeach
</div>

 {{-- Related Products --}}
@if(isset($related) && $related->count())
<div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="m-0">Related Products</h4>
        @if($product->category)
            <a href="{{ route('products.filter', $product->category->slug) }}" class="small">See more</a>
        @endif
    </div>

    <div class="overflow-auto" style="white-space: nowrap; padding-bottom: 1rem;">
        @foreach($related as $rp)
            @php
                $img = $rp->image_url ?? asset('storage/products/default-image.jpg');
                $active = in_array((int)$rp->id, $wishlistIds, true);
            @endphp
            <div class="card d-inline-block me-3" style="width: 18%; min-width: 200px;">
                <div class="position-relative">
                    <a href="{{ route('products.show', $rp->slug) }}">
                        <img src="{{ $img }}" alt="{{ $rp->title }}" class="card-img-top" style="aspect-ratio:4/3; object-fit:cover;">
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
                <div class="card-body p-2">
                    <div class="small text-muted mb-1">{{ optional($rp->category)->name ?? '—' }}</div>
                    <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($rp->title, 40) }}</div>
                    <div class="mt-1">₹{{ number_format($rp->price, 2) }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@include('partials.recently-viewed')


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

<script>
document.getElementById('review-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await res.json();

        if (res.ok && data.ok) {
            const reviewsList = document.getElementById('reviews-list');
            const existingReview = document.getElementById('review-' + data.review.id);

            const html = `
                <div class="mb-3 border-bottom pb-2 review" id="review-${data.review.id}">
                    <strong>${data.review.user_name}</strong>
                <span class="text-warning ms-2">
                    ${[1,2,3,4,5].map(i => 
                        `<i class="fa${i <= data.review.rating ? 's' : 'r'} fa-star"></i>`
                    ).join('')}
                </span>
                    <p>${data.review.comment ?? ''}</p>
                </div>
            `;

            if (existingReview) {
                existingReview.outerHTML = html; // update existing
            } else {
                reviewsList.insertAdjacentHTML('afterbegin', html); // add new
            }

            form.reset(); // reset form
        } else {
            alert(data.message || 'Error submitting review');
        }
    } catch(err) {
        console.error(err);
        alert('Error submitting review');
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('#star-rating .star');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;

            // reset colors
            stars.forEach(s => s.style.color = '#ccc');

            // highlight selected stars
            for (let i = 0; i < value; i++) {
                stars[i].style.color = '#ffc107'; // Bootstrap warning color (gold)
            }
        });
    });
});
</script>


@endpush
