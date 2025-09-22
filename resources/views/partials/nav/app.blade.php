@php
use Illuminate\Support\Facades\Auth;
use App\Models\WishlistItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

$navbarClass = 'navbar navbar-light bg-light fixed-top border-bottom shadow-sm';

$isHome = request()->routeIs('homi');

// Detect auth screens, or allow a manual override via include(..., ['forceMinimal' => true])
$isAuthScreen = request()->routeIs('login') || request()->routeIs('register');
$minimalNav   = $isAuthScreen || (!empty($forceMinimal) && $forceMinimal);

// Normal controls (used when NOT minimal)
$wishlistCount = session('wishlist.count')
    ?? (is_array(session('wishlist.items')) ? count(session('wishlist.items')) : 0);
$cartCount = collect(session('cart.items', []))->sum('qty');

// Show category dropdown ONLY on product pages (never on Home)
$showCatDropdown = request()->routeIs('products.*');

// Home-only preview data
$wishlistProducts = collect();
$cartProducts     = collect();
$cartQtyById      = collect();

if ($isHome && !$minimalNav) {
  $wishlistIds = collect(session('wishlist.items', []))
    ->map(fn($v) => (int) (is_array($v) ? ($v['id'] ?? $v['product_id'] ?? null) : $v))
    ->filter()->unique()->take(8)->values()->all();
  if (!empty($wishlistIds)) {
    $wishlistProducts = Product::whereIn('id', $wishlistIds)->get();
  }

  $cartItems = collect(session('cart.items', []));
  $cartIds = $cartItems
    ->map(fn($i) => (int) (is_array($i) ? ($i['id'] ?? $i['product_id'] ?? null) : $i))
    ->filter()->unique()->values()->all();
  $cartQtyById = $cartItems->mapWithKeys(function ($i) {
    $id  = (int) (is_array($i) ? ($i['id'] ?? $i['product_id'] ?? null) : $i);
    $qty = (int) (is_array($i) ? ($i['qty'] ?? 1) : 1);
    return $id ? [$id => $qty] : [];
  });
  if (!empty($cartIds)) {
    $cartProducts = Product::whereIn('id', $cartIds)->get();
  }
}
@endphp

<style>
  /* tiny icon badges (kept for non-minimal pages) */
  .navbar .icon-btn{
    position: relative;
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: .5rem; text-decoration: none; color: #212529;
  }
  .navbar .icon-btn:hover{ background: rgba(0,0,0,.06); }
  .navbar .icon{ width: 18px; height: 18px; display:block; }
  .navbar .icon-toggle::after{ display:none !important; }
  .navbar .icon-badge{
    position: absolute; top: 0; right: 0;
    transform: translate(35%,-35%);
    min-width: 14px; height:14px; line-height:14px;
    font-size: .6rem; border-radius: 999px; padding: 0 3px;
    background:#dc3545; color:#fff; font-weight:700; text-align:center;
  }
  .navbar .dropdown-menu {
    position: absolute !important;
    top: 100% !important;
    left: 0;
    z-index: 1050; /* above navbar */
  }
</style>

<nav class="{{ $navbarClass }}">
  <div class="container-fluid d-flex justify-content-between align-items-center">

    {{-- LEFT --}}
    <ul class="navbar-nav flex-row align-items-center">
      {{-- Blog (leftmost) --}}
      <li class="nav-item me-3">
        <a class="nav-link {{ request()->routeIs('blog.*') ? 'fw-semibold active' : '' }}"
           href="{{ route('blog.index') }}">Blog</a>
      </li>

      {{-- Home --}}
      <li class="nav-item me-3">
        <a class="nav-link {{ $isHome ? 'fw-semibold active' : '' }}" href="{{ route('homi') }}">Home</a>
      </li>
      {{-- Product --}}
        <li class="nav-item me-3">
          <a class="nav-link {{ request()->routeIs('products.*') ? 'fw-semibold active' : '' }}"
             href="{{ route('products.index') }}">Products</a>
        </li>

        {{-- Category dropdown only on product pages --}}
        @if($showCatDropdown)
          <li class="nav-item dropdown me-3">
            <a class="nav-link dropdown-toggle"
               href="#" id="catDropdown" role="button"
               data-bs-toggle="dropdown" data-bs-auto-close="outside"
               aria-expanded="false">
              Product Categories
            </a>
            <ul class="dropdown-menu shadow p-2"
                aria-labelledby="catDropdown"
                style="min-width:240px; max-height:60vh; overflow-y:auto;">
              <li><a class="dropdown-item" href="{{ route('products.index') }}">All Products</a></li>
              @foreach(($categories ?? []) as $category)
                <li>
                  <a class="dropdown-item" href="{{ route('products.filter', $category->slug) }}">
                    {{ $category->name }}
                  </a>
                </li>
              @endforeach
            </ul>
          </li>
        @endif
    </ul>

    {{-- RIGHT --}}
    @if(!$minimalNav)
      <ul class="navbar-nav flex-row align-items-center">
        {{-- (Home-only) Wishlist + Cart previews --}}
        @if($isHome)
          {{-- Wishlist (only for logged in users) --}}
          @auth
            <li class="nav-item me-2 position-relative">
  <a class="icon-btn" href="{{ route('wishlist.index') }}" title="Wishlist" aria-label="Wishlist">
    <svg class="icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
      <path d="M8 2.748-.717-.737C5.6-.281 8 .522 8 3.314 8 .522 10.4-.28 12.717 2.01 15.6 4.905 8 12 8 12s-7.6-7.095-4.717-9.99z"/>
    </svg>
    <span id="wishlist-count-badge"
          class="icon-badge {{ $wishlistCount > 0 ? '' : 'd-none' }}">
      {{ $wishlistCount }}
    </span>
  </a>
</li>

          @endauth

          {{-- Cart (always visible on home) --}}
          <li class="nav-item me-3">
            <a class="icon-btn" href="{{ route('cart.index') }}" title="Cart" aria-label="Cart">
              <svg class="icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h2A1.5 1.5 0 0 1 14 5.5v8A1.5 1.5 0 0 1 12.5 15h-9A1.5 1.5 0 0 1 2 13.5v-8A1.5 1.5 0 0 1 3.5 4h2v-.5A2.5 2.5 0 0 1 8 1Zm1.5 3v-.5a1.5 1.5 0 1 0-3 0V4h3Z"/>
              </svg>
              <span id="cart-badge" class="icon-badge {{ $cartCount>0 ? '' : 'd-none' }}">
                {{ $cartCount }}
              </span>
            </a>
          </li>
        @endif

        @guest
          <li class="nav-item me-2"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('customer.register') }}">Register (Customer)</a></li>
        @endguest

        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">
              {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
            <li>
              @if(Auth::check() && Auth::user()->role === 'admin')
                <a class="dropdown-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                  href="{{ route('admin.profile.edit') }}">Profile</a>
              @else
                <a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                  href="{{ route('profile.index') }}">Profile</a>
              @endif
            </li>              
            <li>
              @if(Auth::check() && Auth::user()->role === 'admin')
                <a class="dropdown-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                  href="{{ route('admin.settings.index') }}">Settings</a>
              @else
                <a class="dropdown-item {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                  href="{{ route('settings.index') }}">Settings</a>
              @endif
            </li>
                <form method="POST" action="{{ route('logout') }}">@csrf
                  <button class="dropdown-item" type="submit">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @endauth
      </ul>
    @endif

  </div>
</nav>
