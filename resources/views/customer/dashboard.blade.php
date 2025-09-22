@extends('lay_app')

@section('title', 'Customer Dashboard')

{{-- LEFT SIDEBAR --}}
@section('sidebar')
  @include('partials.sidebar.customer')
@endsection

{{-- MAIN CONTENT --}}
@push('styles')
<style>
  /* one-screen canvas (between fixed navbar and fixed footer) */
  .dashboard-wrap{
    height: calc(100vh - var(--nav-h) - var(--footer-h));
    display: flex;
    gap: 32px;
    align-items: center;
    overflow: hidden; /* no page scroll */
  }

  /* LEFT: compact text column (small) */
  .dashboard-text{
    flex: 0 1 28%;      /* smaller column (28%) */
    min-width: 240px;
    height: 100%;
    display: flex; flex-direction: column;
  }
  .dashboard-heading{
    margin-top: 25px;   /* space below navbar */
    margin-bottom: .4rem;
    font-size: 2rem;
    font-weight: 600;
  }
  .dashboard-copy{
    flex: 1 1 auto;
    overflow: auto;     /* only this can scroll if message is long */
    padding-right: 6px;
    font-size: .9rem;   /* smaller text */
    line-height: 1.55;
    color: #374151;
  }

  /* RIGHT: BIG visual (dominant) */
  .dashboard-visual{
    flex: 1 1 72%;      /* larger column (72%) */
    height: 100%;
    display: flex; align-items: center; justify-content: center;
  }
  .dashboard-visual img{
    width: 90%;        /* fill the column (allows upscale) */
    max-height: 90%;
    height: auto;
    object-fit: contain;
    border-radius: 10px;
  }

  @media (max-width: 992px){
    .dashboard-text{ flex-basis: 32%; }
    .dashboard-visual{ flex-basis: 68%; }
  }
</style>
@endpush

@section('content')
  <div class="dashboard-wrap">
    {{-- LEFT: compact text --}}
    <div class="dashboard-text">
      <h1 class="dashboard-heading">Customer Dashboard</h1>
      <div class="dashboard-copy">
        <p class="mb-2">
          Welcome! This is your space to manage shopping in one place—view products, track orders,
          maintain your wishlist, update your profile, and reach support.
        </p>
        <p class="mb-2">
          Use the left menu for quick navigation. Cart & wishlist live in the header and update live as you browse.
        </p>
        <p class="mb-0">
          Tip: go to <strong>Products</strong> to filter by category, search, and add items fast. Happy shopping!
        </p>
      </div>
    </div>

    {{-- RIGHT: big image --}}
    <div class="dashboard-visual">
      <img src="{{ asset('images/D-image.jpg') }}" alt="Welcome to your dashboard">
    </div>
  </div>
@endsection
