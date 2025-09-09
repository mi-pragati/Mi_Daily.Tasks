@extends('layouts.customer')
@section('title', 'My Wishlist')

@section('content')
@php
  $wishlistIds = collect(session('wishlist.items', []))->map(fn($v)=>(int)$v)->all();
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
              <form method="POST" action="{{ route('cart.store') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $p->id }}">
                <button class="btn btn-sm btn-primary">Add to cart</button>
              </form>

              {{-- Remove from wishlist (AJAX) --}}
              <button
                type="button"
                class="btn btn-sm btn-outline-danger js-wish-remove"
                data-id="{{ $p->id }}"
                title="Remove"
              >Remove</button>
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
  // Remove from wishlist via your existing /wishlist/toggle endpoint
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-wish-remove');
    if (!btn) return;

    const id = btn.getAttribute('data-id');
    const token = document.querySelector('meta[name="csrf-token"]').content;

    try {
      const res = await fetch(@json(route('wishlist.toggle')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: id }),
      });

      if (!res.ok) throw new Error(await res.text());
      const data = await res.json();
      if (!data.ok) throw new Error('Wishlist update failed');

      // Remove the card from UI
      const cardCol = btn.closest('.col-6, .col-md-4, .col-lg-3') || btn.closest('.card');
      if (cardCol) cardCol.remove();

      // Update navbar wishlist badge if present
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

      // If nothing left, show empty state
      if (document.querySelectorAll('.card').length === 0) {
        location.reload();
      }
    } catch (err) {
      console.error(err);
      alert('Could not update wishlist. Please try again.');
    }
  });
</script>
@endpush
