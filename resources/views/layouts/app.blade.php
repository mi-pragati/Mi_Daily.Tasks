<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog CMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')

      <style>
       .custom-navbar {
            background-color: #e5e7eb !important; /* faint gray */
            padding-top: 1.0rem !important;
            padding-bottom: 1.0rem !important;

        }

        /* Links inside the navbar */
        .custom-navbar a {
            color: #000000 !important; /* black color */
            text-decoration: none !important; /* no underline */
            font-weight: 600; /* bold */
        }

        /* Hover state */
        .custom-navbar a:hover {
            color: #000000 !important; /* keep black on hover */
            text-decoration: none !important;
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased">
    
    <nav class="custom-navbar text-white p-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                {{-- LEFT --}}
             <ul class="navbar-nav flex-row align-items-center">
                <a href="{{ route('blog.index') }}" class="mr-4">Blog</a>
                <span class="text-muted mx-3">|</span>
                <a href="{{ route('homi') }}" class="mr-4">Home</a>
                <span class="text-muted mx-3">|</span>
                <a href="{{ route('products.index') }}" class="mr-4">Product</a>
                <span class="text-muted mx-3">|</span>

            @auth
                 @if(auth()->user()->isEditor())
                 <a href="{{ route('editor.posts.create') }}" class="mr-4">Create Post</a>
                  @endif
                  @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="mr-4">Admin Dashboard</a>
                     @endif
                    @endauth
    </ul>

            </div>
            <div>
                @guest
                    <a href="{{ route('login') }}" class="mr-2">Login</a>
                    
            
                @else
                    <span class="mr-2 text-black">Hello, {{ auth()->user()->name }}</span>
                    <span class="mx-3 text-black">|</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf 
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-black">Logout</button>
                    </form>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <main class="px-2 mt-4" style="margin-left: 0 !important; padding-left: 10px !important; max-width: 100%;">
    @if(session('success')) 
        <div class="bg-green-200 p-2 mb-4">{{ session('success') }}</div> 
    @endif
    @yield('content')
</main>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
