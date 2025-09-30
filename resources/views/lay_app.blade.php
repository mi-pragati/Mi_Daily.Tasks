<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <title>@yield('title', 'Blog CMS')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @stack('styles')

  <style>
    /* ===== Base + theme tokens ===== */
    html, body { max-width: 100vw; overflow-x: hidden; }
    body { --nav-h: 56px; padding-top: var(--nav-h); }

    :root{
      --nav-bg:#e9ecef; --nav-fg:#212529; --nav-hover-bg:rgba(0,0,0,.08);
      --footer-fg:#5c636a; --footer-link:#343a40; --bar-border:#d1d5db;
      --sidebar-w:260px;
      --footer-h:64px; /* keep in sync with footer height */
    }

    /* ===== Navbar ===== */
    .navbar.fixed-top{ z-index:1100; }
    .navbar.bg-light, .navbar.navbar-light { background-color: var(--nav-bg) !important; }
    .navbar.bg-light { border-bottom: 1px solid var(--bar-border) !important; }
    .navbar.bg-light .nav-link{ color:var(--nav-fg)!important; padding:.5rem .75rem; }
    .navbar.bg-light .nav-link.active{ font-weight:600; }

    /* (Optional) if you ever use icon buttons from the nav partial outside Home */
    .navbar .icon-btn{ position:relative; width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:.5rem; text-decoration:none; color:#212529; }
    .navbar .icon-btn:hover{ background:var(--nav-hover-bg); }
    .navbar .icon{ width:18px; height:18px; display:block; }
    .navbar .icon-badge, .nav-badge{
      position:absolute; top:0; right:0; transform: translate(35%,-35%);
      min-width:14px; height:14px; line-height:14px; font-size:.6rem;
      border-radius:999px; padding:0 3px; background:#dc3545; color:#fff; font-weight:700; text-align:center;
    }

    /* ===== Fixed sidebar (stops above footer) ===== */
    #app-sidebar{
      position: fixed;
      top: var(--nav-h);
      left: 0;
      bottom: var(--footer-h);       /* leaves space for fixed footer */
      width: var(--sidebar-w);
      overflow: hidden;              /* scroll only inner container */
      background: #f8f9fa;
      border-right: 1px solid #e9ecef;
      z-index: 1030;                 /* below navbar & footer */
    }
    /* Scroll area inside sidebar + spacing */
    #app-sidebar .sb-scroll{
      height: calc(100vh - var(--nav-h) - var(--footer-h));
      overflow-y: auto;
      padding: 12px 14px;
    }
    /* Sidebar links look + active state */
    .sb-menu .nav-link{
      display:flex; align-items:center; gap:.5rem;
      padding:.55rem .6rem; border-radius:.5rem;
      color:#374151;
    }
    .sb-menu .nav-link:hover{ background:#f1f5f9; color:#111827; }
    .sb-menu .nav-link.active{
      background:#e2e8f0; color:#111827; font-weight:600;
      box-shadow: inset 2px 0 0 #0d6efd;
    }
    .sb-menu .ico{ width:18px; height:18px; flex:0 0 18px; opacity:.75; }
    .sb-menu .nav-link.active .ico{ opacity:1; }

    /* ===== Main content ===== */
    main.app-main{
      margin-left: 0;
      width: 100%;
      max-width: 100%;
      padding-bottom: calc(var(--footer-h) + 8px); /* never under footer */
      min-height: calc(100vh - var(--nav-h) - var(--footer-h));
      box-sizing: border-box;
    }
    main.app-main.has-sidebar{
      margin-left: var(--sidebar-w);
      width: calc(100% - var(--sidebar-w));
      max-width: calc(100% - var(--sidebar-w));
    }
    main.vh-fit{
      height: calc(100vh - var(--nav-h) - var(--footer-h));
      min-height: calc(100vh - var(--nav-h) - var(--footer-h));
      overflow: hidden; /* no page scroll on the dashboard */
    }
    .app-main .container, .app-main .container-fluid { max-width: 100%; }

    /* ===== Fixed footer (full width, above sidebar) ===== */
    .app-footer-wrap{
      position: fixed;
      left: 0; right: 0; bottom: 0;
      width: 100%;
      z-index: 1080; /* above sidebar */
    }
    .app-footer{
      background: var(--nav-bg) !important;
      color: var(--footer-fg);
      border-top: 1px solid var(--bar-border) !important;
      min-height: var(--footer-h);
      display: flex; align-items: center;
    }
    .app-footer .footer-line{ border-color: var(--bar-border) !important; }
    .app-footer a.footer-link{ color: var(--footer-link); text-decoration:none; }
    .app-footer a.footer-link:hover{ color:#212529; text-decoration:underline; }
  </style>
</head>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateWishlistCount() {
        fetch("{{ route('wishlist.count') }}", {
    method: 'GET',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(async response => {
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch {
        throw new Error("Invalid JSON: " + text.substring(0, 100));
    }
})
.then(data => {
    if (data.ok) {
        const badge = document.getElementById('wishlist-count-badge');
        badge.textContent = data.count;
        badge.style.display = (data.count > 0) ? 'inline-block' : 'none';
    }
})
.catch(err => console.error('Wishlist count fetch error:', err));


    updateWishlistCount(); // initial load

    // OPTIONAL: Auto-refresh count every 30 seconds
    setInterval(updateWishlistCount, 30000);
});
</script>

<body class="antialiased">
  @include('partials.nav.app')

  <div class="container-fluid">
    <div class="row">
      {{-- Sidebar slot --}}
      @hasSection('sidebar')
        <aside id="app-sidebar">@yield('sidebar')</aside>
      @else
        @auth
          @php
            // Keep products pages guest-like (no sidebar there)
            $showSidebarForLoggedIn = request()->routeIs('customer.*')
              || request()->routeIs('profile.*')
              || request()->routeIs('cart.*');
          @endphp
          @if($showSidebarForLoggedIn)
            <aside id="app-sidebar">
              @include('partials.sidebar.customer', ['categories' => $categories ?? []])
            </aside>
          @endif
        @endauth
      @endif

      @php
        $__hasSidebar =
          View::hasSection('sidebar') ||
          (auth()->check() && (
            request()->routeIs('customer.*') ||
            request()->routeIs('profile.*')  ||
            request()->routeIs('cart.*')
          ));
      @endphp

      @php $isDashboard = request()->routeIs('customer.dashboard'); @endphp
      <main class="app-main px-3 {{ $__hasSidebar ? 'has-sidebar' : '' }} {{ $isDashboard ? 'vh-fit' : '' }}">
        @yield('content')
      </main>
    </div>
  </div>

  {{-- fixed footer (above sidebar) --}}
  <div class="app-footer-wrap">
    @include('partials.footer.app')
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
