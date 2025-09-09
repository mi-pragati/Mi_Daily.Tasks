<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Editor')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .avatar-badge{
      width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;
      border-radius:50%;font-weight:700;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="{{ route('editor.home') }}">Editor</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ednav"
            aria-controls="ednav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="ednav" class="collapse navbar-collapse">
      {{-- left side --}}
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('editor.home') }}">Home</a></li>
      </ul>

      {{-- right side --}}
      <ul class="navbar-nav ms-auto align-items-center">
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="avatar-badge bg-primary text-white">
                {{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
              </span>
              <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow">
              <li><a class="dropdown-item" href="{{ route('editor.posts.create') }}">Create Post</a></li>

            @php $currentPost = request()->route('post');
            @endphp
            @can('update', $currentPost)
            <li>
            <a class="dropdown-item" href="{{ route('editor.posts.edit', $currentPost) }}">
            Edit Post</a></li>
@endcan
              <li><a class="dropdown-item" href="{{ route('posts.mine') }}">My Posts</a></li>
              <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">@csrf
                  <button class="dropdown-item">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @else
          {{-- guests (shouldn’t hit /editor, but safe fallback) --}}
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<main class="py-4">
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
