<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog CMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="antialiased">
    <nav class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <div>
                <a href="{{ route('posts.index') }}" class="mr-4">Home</a>
                <span class="text-muted mx-3">|</span>
                @auth
                    <a href="{{ route('editor.posts.create') }}" class="mr-4">Create Post</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="mr-4">Admin Dashboard</a>
                    @endif
                @endauth
            </div>
            <div>
                @guest
                    <a href="{{ route('login') }}" class="mr-2">Login</a>
                    <span class="text-muted mx-3">|</span>
                    <a href="{{ route('register') }}" class="mr-2">Register (Editor)</a>
                    <span class="text-muted mx-3">|</span>
                    <a href="{{ route('admin.register.form') }}" class="mr-2">Register (Admin)</a>
                @else
                    <span class="mr-2">Hello, {{ auth()->user()->name }}</span>
                    <span class="text-muted mx-3">|</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf <button type="submit">Logout</button></form>
                @endguest
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6">
        @if(session('success')) <div class="bg-green-200 p-2 mb-4">{{ session('success') }}</div> @endif
        @yield('content')
    </main>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
