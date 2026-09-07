<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;
            background: #f4f5f7;
            color: #1f2328;
        }

        /* ===== Sidebar ===== */
        .admin-sidebar {
            width: 240px;
            min-height: 100vh;
            background: #16181d;
            padding: 24px 16px;
            position: sticky;
            top: 0;
        }
        .admin-sidebar h5 {
            color: #fff;
            font-weight: 700;
            font-size: 17px;
            margin-bottom: 28px;
            padding: 0 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-sidebar .nav-link {
            color: #aeb4bd;
            font-size: 14.5px;
            font-weight: 500;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all .15s ease;
        }
        .admin-sidebar .nav-link i { font-size: 16px; width: 18px; text-align: center; }
        .admin-sidebar .nav-link:hover { background: #24272e; color: #fff; }
        .admin-sidebar .nav-link.active { background: #e30613; color: #fff; }
        .admin-sidebar .btn-outline-light {
            border-color: #333; font-size: 13.5px; font-weight: 600; border-radius: 8px;
        }
        .admin-sidebar .btn-outline-light:hover { background: #e30613; border-color: #e30613; }

        /* ===== Main content ===== */
        .admin-main { flex: 1; padding: 32px 36px; }
        .admin-main h4, .admin-main h1 { font-weight: 700; letter-spacing: -0.3px; margin-bottom: 24px; }

        /* Card wrapper so every form/table sits on a clean white surface */
        .admin-card {
            background: #fff;
            border: 1px solid #e8e9ec;
            border-radius: 12px;
            padding: 28px 30px;
            box-shadow: 0 1px 3px rgba(20,20,30,0.04);
        }

        /* Form polish */
        .admin-main label { font-weight: 600; font-size: 13.5px; color: #444; margin-bottom: 6px; display: inline-block; }
        .admin-main .form-control, .admin-main .form-select {
            border-radius: 8px; border: 1px solid #d9dce1; padding: 9px 13px; font-size: 14.5px;
        }
        .admin-main .form-control:focus, .admin-main .form-select:focus {
            border-color: #e30613; box-shadow: 0 0 0 3px rgba(227,6,19,0.1);
        }
        .admin-main .btn-primary {
            background: #e30613; border-color: #e30613; font-weight: 600; padding: 9px 24px; border-radius: 8px;
        }
        .admin-main .btn-primary:hover { background: #c00510; border-color: #c00510; }

        /* Tabs (Nepali / English) */
        .nav-tabs { border-bottom: 2px solid #eee; gap: 4px; }
        .nav-tabs .nav-link {
            border: none; border-radius: 8px 8px 0 0; font-weight: 600; font-size: 14.5px;
            color: #888; padding: 10px 20px;
        }
        .nav-tabs .nav-link.active {
            color: #e30613; background: #fff5f5; border-bottom: 2px solid #e30613; margin-bottom: -2px;
        }
        .nav-tabs .nav-link:hover:not(.active) { background: #f7f7f8; }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <h5><i class="bi bi-newspaper"></i> Admin Panel</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">
                    <i class="bi bi-file-earmark-text"></i> News
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}" href="{{ route('admin.ads.index') }}">
                    <i class="bi bi-megaphone"></i> Ads
                </a>
            </li>
            <li class="nav-item mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-main">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>