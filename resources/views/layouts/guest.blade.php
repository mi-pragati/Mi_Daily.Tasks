<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Bootstrap (needed by the shared navbar/footer partials) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Vite (Tailwind etc.) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      /* Same gray theme + spacing used in the main layout */
      :root{
        --nav-bg:#e9ecef;
        --nav-fg:#212529;
        --nav-hover-bg:rgba(0,0,0,.08);
        --footer-fg:#5c636a;
        --footer-link:#343a40;
        --bar-border:#d1d5db;
        --nav-h:56px;
      }
      html, body { max-width: 100vw; overflow-x: hidden; }
      body { padding-top: var(--nav-h); }

      /* Navbar tint match */
      .navbar.bg-light, .navbar.navbar-light { background-color: var(--nav-bg) !important; }
      .navbar.bg-light { border-bottom: 1px solid var(--bar-border) !important; }
      .navbar.bg-light .nav-link { color: var(--nav-fg) !important; }
      .navbar.bg-light .nav-link.active { font-weight: 600; }

      /* Footer tint match */
      .app-footer { background: var(--nav-bg) !important; color: var(--footer-fg); }
      .app-footer { border-top: 1px solid var(--bar-border) !important; }
      .app-footer .footer-line { border-color: var(--bar-border) !important; }
      .app-footer a.footer-link { color: var(--footer-link); text-decoration: none; }
      .app-footer a.footer-link:hover { color:#212529; text-decoration: underline; }
    </style>
  </head>
  <body class="font-sans text-gray-900 antialiased">

    {{-- Minimal nav on auth pages (only “Home”) --}}
    @include('partials.nav.app', ['forceMinimal' => true])

    {{-- Auth card wrapper (Tailwind) --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

      <div>
        <a href="{{ route('homi') }}">
          <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
        </a>
      </div>

      <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
      </div>

    </div>

    {{-- Shared footer (same gray as header) --}}
    @include('partials.footer.app')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
