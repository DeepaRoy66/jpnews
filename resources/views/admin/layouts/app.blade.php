<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-dark text-white p-3" style="width:220px; min-height:100vh;">
        <h5 class="mb-4">📰 Admin Panel</h5>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.categories.index') }}">Categories</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.news.index') }}">News</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.ads.index') }}">Ads</a></li>
            <li class="nav-item mt-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-outline-light w-100">Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        @yield('content')
    </div>
</div>
</body>
</html>
