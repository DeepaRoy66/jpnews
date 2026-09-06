<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MeroNews')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; }

        /* Breaking news ticker */
        .breaking-bar {
            background: #111;
            color: #fff;
            display: flex;
            align-items: center;
            overflow: hidden;
            height: 42px;
        }
        .breaking-badge {
            background: #e30613;
            color: #fff;
            font-weight: 700;
            padding: 8px 18px;
            white-space: nowrap;
            flex-shrink: 0;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .breaking-ticker {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            position: relative;
        }
        .breaking-ticker-track {
            display: inline-block;
            padding-left: 100%;
            animation: ticker 30s linear infinite;
        }
        .breaking-ticker-track span {
            margin-right: 60px;
            font-size: 14px;
        }
        @keyframes ticker {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        /* Header */
        .site-header { padding: 18px 0 10px; }
        .social-icons a {
            color: #333;
            margin-right: 14px;
            font-size: 18px;
        }
        .social-icons a:hover { color: #e30613; }
        .site-logo img { max-height: 60px; }
        .header-actions a {
            color: #333;
            margin-left: 16px;
            font-size: 18px;
            cursor: pointer;
        }
        .site-date {
            text-align: center;
            font-size: 14px;
            color: #666;
            padding: 6px 0;
            border-top: 1px solid #eee;
        }

        /* Category nav */
        .category-nav {
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .category-nav .nav-link {
            color: #222;
            font-weight: 600;
            padding: 12px 16px;
        }
        .category-nav .nav-link:hover,
        .category-nav .nav-link.active {
            color: #e30613;
        }
    </style>
</head>
<body>

{{-- Breaking News Ticker --}}
<div class="breaking-bar">
    <div class="breaking-badge">🔴 ब्रेकिङ</div>
    <div class="breaking-ticker">
        <div class="breaking-ticker-track">
            @isset($breakingNews)
                @foreach($breakingNews as $item)
                    <span>{{ $item->title }}</span>
                @endforeach
            @else
                <span>ताजा समाचारका लागि हाम्रो साइटमा भ्रमण गर्नुहोस्</span>
            @endisset
        </div>
    </div>
</div>

{{-- Main Header --}}
<div class="container site-header">
    <div class="row align-items-center">
        <div class="col-4 social-icons">
            <a href="#"><i class="bi bi-facebook">FB</i></a>
            <a href="#"><i class="bi bi-instagram">IG</i></a>
            <a href="#"><i class="bi bi-youtube">YT</i></a>
            <a href="#"><i class="bi bi-twitter-x">X</i></a>
        </div>
        <div class="col-4 text-center site-logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                <div class="fw-bold fs-4">📰 MeroNews</div>
            </a>
        </div>
        <div class="col-4 text-end header-actions">
            <a href="#" title="Menu">☰</a>
            <a href="#" title="Search">🔍</a>
            <a href="#" id="themeToggle" title="Dark Mode">🌙</a>
        </div>
    </div>
</div>

{{-- Date --}}
<div class="site-date">
    {{ now()->format('l, F j, Y') }}
</div>

{{-- Category Navigation --}}
<nav class="category-nav">
    <div class="container">
        <ul class="nav justify-content-center">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">ताजा समाचार</a></li>
            @isset($categories)
                @foreach($categories as $cat)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a>
                    </li>
                @endforeach
            @endisset
        </ul>
    </div>
</nav>

{{-- Navbar Ad Banner (admin panel bata control huney) --}}
@if($navbarAd)
<div class="text-center bg-light py-2">
    <a href="{{ $navbarAd->link ?? '#' }}" target="_blank" rel="noopener">
        <img src="{{ asset('storage/'.$navbarAd->image) }}" alt="{{ $navbarAd->title }}" style="max-height:90px;">
    </a>
</div>
@endif

<div class="container my-4">
    @yield('content')
</div>

<script>
    document.getElementById('themeToggle').addEventListener('click', function (e) {
        e.preventDefault();
        document.body.classList.toggle('bg-dark');
        document.body.classList.toggle('text-white');
    });
</script>

</body>
</html>