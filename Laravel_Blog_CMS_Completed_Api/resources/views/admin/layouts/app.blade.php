<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Admin')</title>
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
    <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Admin</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adnav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="adnav" class="collapse navbar-collapse">
      {{-- LEFT: only Home | Admin Dashboard --}}
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('home') }}">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
             href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
        </li>
      </ul>

      {{-- RIGHT: profile dropdown with all actions --}}
      <ul class="navbar-nav ms-auto">
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
              <span class="avatar-badge bg-primary text-white">
                {{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
              </span>
              <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li class="dropdown-header">Manage</li>
              <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">Categories</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.posts.index') }}">Posts</a></li>

              <li><hr class="dropdown-divider"></li>
              <li class="dropdown-header">Quick actions</li>
              <li><a class="dropdown-item" href="{{ route('admin.posts.create') }}">Create Post</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.categories.create') }}">Create Category</a></li>

              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}">Profile</a></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">@csrf
                  <button class="dropdown-item">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<main class="py-4">
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
