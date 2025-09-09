@php
  // counts pulled from session
  $wishlistCount = session('wishlist.count', 0);
  $cartCount     = collect(session('cart.items', []))->sum('qty');
@endphp

<nav class="navbar navbar-dark bg-primary fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">

       <ul class="navbar-nav flex-row">
      <li class="nav-item me-3">
          <a class="nav-link {{ request()->routeIs('homi') ? 'active' : '' }}" href="{{ route('homi') }}">Home</a>
        </li>
         </li>
      <li class="nav-item me-3">
        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
           href="{{ route('products.index') }}">Products</a>
      </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
             href="{{ route('customer.dashboard') }}">
            Dashboard
          </a>
        </li>
      </ul>

      {{-- RIGHT: small icons (wishlist + cart) + user menu --}}
          <ul class="navbar-nav flex-row align-items-center">
              <li class="nav-item me-2">
          <a class="nav-ico-btn" href="{{ route('wishlist.index') }}" title="Wishlist" aria-label="Wishlist">
            <svg class="nav-ico" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
              <path d="M8 2.748-.717-.737C5.6-.281 8 .522 8 3.314 8 .522 10.4-.28 12.717 2.01 15.6 4.905 8 12 8 12s-7.6-7.095-4.717-9.99z"/>
            </svg>
            {{-- make badge addressable for live updates --}}
            @if($wishlistCount > 0)
              <span id="wishlist-badge" class="nav-badge">{{ $wishlistCount }}</span>
            @else
              <span id="wishlist-badge" class="nav-badge d-none">0</span>
            @endif
          </a>
        </li>

             <li class="nav-item me-3">
          <a class="nav-ico-btn" href="{{ route('cart.index') }}" title="Cart" aria-label="Cart">
            <svg class="nav-ico" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
              <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h2A1.5 1.5 0 0 1 14 5.5v8A1.5 1.5 0 0 1 12.5 15h-9A1.5 1.5 0 0 1 2 13.5v-8A1.5 1.5 0 0 1 3.5 4h2v-.5A2.5 2.5 0 0 1 8 1Zm1.5 3v-.5a1.5 1.5 0 1 0-3 0V4h3Z"/>
            </svg>
            @if($cartCount > 0)
              <span id="cart-badge" class="nav-badge">{{ $cartCount }}</span>
            @else
              <span id="cart-badge" class="nav-badge d-none">0</span>
            @endif
          </a>
        </li>

        {{-- User dropdown --}}
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">
            {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">Profile</a></li>
            <li><a class="dropdown-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="dropdown-item" type="submit">Logout</button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
