{{-- Sidebar (same look & feel as Profile sidebar) --}}
<style>
  /* Scoped sidebar styling (kept inside the partial to avoid global bleed) */
  #app-sidebar .sb-scroll{
    height: 100%;
    padding: 12px;
    display: flex;
    flex-direction: column;
  }
  #app-sidebar .sb-menu{ gap: .25rem; }
  #app-sidebar .sb-menu .nav-link{
    display: flex; align-items: center; gap: .5rem;
    padding: .65rem .75rem;
    border-radius: .5rem;
    color: #212529; text-decoration: none;
    transition: background-color .15s ease, color .15s ease;
  }
  #app-sidebar .sb-menu .nav-link:hover{ background: rgba(0,0,0,.05); }
  #app-sidebar .sb-menu .nav-link.active{
    background: #e9ecef; font-weight: 600;
  }
  #app-sidebar .sb-menu .nav-link .ico{
    flex: 0 0 18px; width: 18px; height: 18px;
    opacity: .7;
  }
  #app-sidebar hr{ border-color: rgba(0,0,0,.1) !important; }
  #app-sidebar .btn-outline-danger{ border-radius: .5rem; }
</style>

<aside id="app-sidebar" aria-label="Customer navigation">
  <div class="sb-scroll">

    <ul class="nav flex-column sb-menu">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
           href="{{ route('customer.dashboard') }}"
           @if(request()->routeIs('customer.dashboard')) aria-current="page" @endif>
          <svg class="ico" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M1 8l7-5 7 5v6H1z"/>
          </svg>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
           href="{{ route('profile.edit') }}"
           @if(request()->routeIs('profile.*')) aria-current="page" @endif>
          <svg class="ico" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M8 9a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7zM2 14c0-2.5 3-4 6-4s6 1.5 6 4v1H2z"/>
          </svg>
          <span>Profile</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"
           href="{{ route('customer.orders.index') }}"
           @if(request()->routeIs('orders.*')) aria-current="page" @endif>
          <svg class="ico" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M1 2h2l1 10h9l2-7H4"/>
            <circle cx="6" cy="14" r="1"/><circle cx="12" cy="14" r="1"/>
          </svg>
          <span>Orders</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}"
           href="{{ route('wishlist.index') }}"
           @if(request()->routeIs('wishlist.*')) aria-current="page" @endif>
          <svg class="ico" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M8 14s-6-4.5-6-8A3.5 3.5 0 0 1 8 4a3.5 3.5 0 0 1 6 2c0 3.5-6 8-6 8z"/>
          </svg>
          <span>Wishlist</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}"
           href="{{ route('cart.index') }}"
           @if(request()->routeIs('cart.*')) aria-current="page" @endif>
          <svg class="ico" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M2 3h1l1 9h8l2-6H5"/>
            <circle cx="6" cy="14" r="1"/><circle cx="11" cy="14" r="1"/>
          </svg>
          <span>Cart</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}"
           href="{{ route('messages.index') }}"
           @if(request()->routeIs('messages.*')) aria-current="page" @endif>
          <svg class="ico" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M1 3h14v8H5l-4 3z"/>
          </svg>
          <span>Messages</span>
        </a>
      </li>
    </ul>

    <hr class="my-3">

    <form method="POST" action="{{ route('logout') }}" class="mt-auto">
      @csrf
      <button type="submit" class="btn btn-outline-danger w-100">Logout</button>
    </form>
  </div>
</aside>
