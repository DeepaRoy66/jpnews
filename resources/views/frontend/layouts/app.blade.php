<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MeroNews')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; color: #222; }
        a { text-decoration: none; }
        .breaking-bar { background: #111; color: #fff; display: flex; align-items: center; overflow: hidden; height: 40px; }
        .breaking-badge { background: #e30613; color: #fff; font-weight: 700; font-size: 14px; padding: 0 18px; white-space: nowrap; flex-shrink: 0; height: 100%; display: flex; align-items: center; }
        .breaking-ticker { overflow: hidden; white-space: nowrap; width: 100%; }
        .breaking-ticker-track { display: inline-block; padding-left: 100%; animation: ticker 35s linear infinite; }
        .breaking-ticker-track span { margin-right: 60px; font-size: 14px; color: #eee; }
        @keyframes ticker { 0% { transform: translateX(0); } 100% { transform: translateX(-100%); } }
        .site-header { padding: 26px 0 14px; }
        .social-icons { display: flex; align-items: center; gap: 16px; }
        .social-icons a { color: #333; font-size: 20px; }
        .social-icons a:hover { color: #e30613; }
        .site-logo { text-align: center; }
        .site-logo a { display: inline-flex; flex-direction: column; align-items: center; gap: 6px; }
        .site-logo img { height: 62px; width: auto; max-width: 230px; object-fit: contain; image-rendering: -webkit-optimize-contrast; transition: transform .2s ease, filter .2s ease; }
        .site-logo a:hover img { transform: scale(1.03); }
        .site-logo .brand-tagline { font-size: 12.5px; color: #888; margin: 0; letter-spacing: .3px; font-weight: 600; }
        .header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 20px; }
        .header-actions a { color: #333; font-size: 20px; cursor: pointer; }
        .header-actions a:hover { color: #e30613; }
        .lang-switch { font-size: 13px !important; font-weight: 700; border: 1.5px solid #ddd; border-radius: 20px; padding: 5px 14px; color: #333 !important; }
        .lang-switch:hover { border-color: #e30613; background: #e30613; color: #fff !important; }
        .site-date { text-align: center; font-size: 14px; color: #666; padding: 8px 0; }
        .category-nav { border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; }
        .category-nav .nav { justify-content: center; flex-wrap: wrap; }
        .category-nav .nav-link { color: #222; font-weight: 600; font-size: 15px; padding: 12px 18px; }
        .category-nav .nav-link:hover, .category-nav .nav-link.active { color: #e30613; }

        .newsletter-strip { background: linear-gradient(90deg, #e30613, #b8040f); color: #fff; padding: 28px 0; }
        .newsletter-strip h5 { font-weight: 800; margin-bottom: 4px; }
        .newsletter-strip p { font-size: 14px; opacity: 0.9; margin-bottom: 0; }
        .newsletter-form input { border: none; border-radius: 4px 0 0 4px; padding: 10px 14px; width: 100%; outline: none; }
        .newsletter-form button { background: #111; color: #fff; border: none; border-radius: 0 4px 4px 0; padding: 10px 20px; font-weight: 700; white-space: nowrap; }
        .newsletter-form button:hover { background: #000; }
        .site-footer { background: #111; color: #b5b5b5; padding: 45px 0 0; }
        .site-footer h5 { color: #fff; font-weight: 700; margin-bottom: 20px; font-size: 16px; position: relative; padding-bottom: 10px; }
        .site-footer h5::after { content: ''; position: absolute; left: 0; bottom: 0; width: 34px; height: 3px; background: #e30613; }
        .site-footer a { color: #b5b5b5; }
        .site-footer a:hover { color: #fff; }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 11px; font-size: 14px; }
        .footer-links li i { color: #e30613; margin-right: 6px; font-size: 13px; }
        .footer-social a { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background: #1e1e1e; border-radius: 50%; margin-right: 8px; font-size: 16px; transition: .2s; }
        .footer-social a:hover { background: #e30613; color: #fff; transform: translateY(-2px); }
        .footer-about p { font-size: 14px; color: #999; line-height: 1.8; }
        .footer-contact li { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; margin-bottom: 12px; }
        .footer-contact i { color: #e30613; margin-top: 3px; }
        .app-badges a { display: inline-flex; align-items: center; gap: 8px; background: #1e1e1e; border: 1px solid #333; border-radius: 6px; padding: 8px 14px; margin-right: 10px; margin-bottom: 10px; font-size: 13px; }
        .app-badges a:hover { border-color: #e30613; }
        .app-badges i { font-size: 20px; }
        .footer-bottom { border-top: 1px solid #262626; margin-top: 35px; padding: 18px 0; font-size: 13px; color: #777; }
        .footer-bottom a { color: #999; margin-left: 16px; }
        .footer-bottom a:hover { color: #fff; }
        #backToTop { position: fixed; bottom: 24px; right: 24px; width: 46px; height: 46px; border-radius: 50%; background: #e30613; color: #fff; display: none; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 999; cursor: pointer; border: none; }
        #backToTop:hover { background: #b8040f; }
    </style>
</head>
<body>

<div class="breaking-bar">
    <div class="breaking-badge">🔴 {{ __('site.breaking') }}</div>
    <div class="breaking-ticker">
        <div class="breaking-ticker-track">
            @forelse($breakingNews as $item)
                <span>{{ $item->title }}</span>
            @empty
                <span>{{ __('site.no_news') }}</span>
            @endforelse
        </div>
    </div>
</div>

<div class="container site-header">
    <div class="row align-items-center">
        <div class="col-4">
            <div class="social-icons">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-youtube"></i></a>
                <a href="#"><i class="bi bi-twitter-x"></i></a>
            </div>
        </div>
        <div class="col-4 site-logo">
            <a href="{{ route('home') }}">
                {{-- 👇 Dummy placeholder — imgbb bata aafno logo link haleर replace garnuhos --}}
                <img src="https://i.ibb.co/0jqz1234/meronews-logo-placeholder.png" alt="MeroNews">
                <p class="brand-tagline">{{ __('site.tagline') }}</p>
            </a>
        </div>
        <div class="col-4">
            @php
                // Build the target URL using whatever scheme/port the
                // visitor is CURRENTLY using — works both in local dev
                // (http + :8000) and production (https, no port needed)
                // without hardcoding a scheme.
                $portSuffix = in_array(request()->getPort(), [80, 443]) ? '' : ':' . request()->getPort();
                $scheme = request()->getScheme();
                $targetHost = app()->getLocale() === 'ne'
                    ? 'english.' . config('app.domain')
                    : config('app.domain');
                $switchUrl = $scheme . '://' . $targetHost . $portSuffix;
            @endphp
            <div class="header-actions">
                @if(app()->getLocale() === 'ne')
                    <a href="{{ $switchUrl }}" class="lang-switch" title="Switch to English">EN</a>
                @else
                    <a href="{{ $switchUrl }}" class="lang-switch" title="{{ __('site.switch_to_nepali') }}">{{ __('site.nepali_label') }}</a>
                @endif
                <a href="#" title="Menu"><i class="bi bi-list fs-3"></i></a>
                <a href="#" title="Search"><i class="bi bi-search"></i></a>
                <a href="#" id="themeToggle" title="Dark Mode"><i class="bi bi-moon"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="site-date">
    {{ now()->format('l, F j, Y') }}
</div>

<nav class="category-nav">
    <div class="container">
        <ul class="nav">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('site.latest_news') }}</a></li>
            @foreach($navCategories as $cat)
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('category/'.$cat->slug) ? 'active' : '' }}" href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>

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

<div class="newsletter-strip">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <h5>{{ __('site.newsletter_title') }}</h5>
                <p>{{ __('site.newsletter_sub') }}</p>
            </div>
            <div class="col-md-6">
                <form class="d-flex newsletter-form" onsubmit="return false;">
                    <input type="email" placeholder="{{ __('site.newsletter_placeholder') }}" required>
                    <button type="submit">{{ __('site.subscribe') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>MeroNews {{ __('site.about_us') }}</h5>
                <div class="footer-about">
                    <p>{{ __('site.about_text') }}</p>
                </div>
                <div class="footer-social mt-3">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>{{ __('site.news_categories') }}</h5>
                <ul class="footer-links">
                    @foreach($navCategories->take(5) as $cat)
                        <li><i class="bi bi-chevron-right"></i><a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>{{ __('site.quick_links') }}</h5>
                <ul class="footer-links">
                    <li><i class="bi bi-chevron-right"></i><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">{{ __('site.about_us') }}</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">{{ __('site.advertise') }}</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">{{ __('site.privacy_policy') }}</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">{{ __('site.contact') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>{{ __('site.contact_address') }}</h5>
                <ul class="footer-links footer-contact">
                    <li><i class="bi bi-geo-alt-fill"></i> {{ __('site.address') }}</li>
                    <li><i class="bi bi-envelope-fill"></i> info@meronews.com</li>
                    <li><i class="bi bi-telephone-fill"></i> {{ __('site.phone') }}</li>
                </ul>
                <h5 class="mt-4">{{ __('site.download_app') }}</h5>
                <div class="app-badges">
                    <a href="#"><i class="bi bi-google-play"></i> Google Play</a>
                    <a href="#"><i class="bi bi-apple"></i> App Store</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center">
            <div>&copy; {{ date('Y') }} MeroNews. {{ __('site.rights_reserved') }}</div>
            <div>
                <a href="#">{{ __('site.privacy_policy') }}</a>
                <a href="#">{{ __('site.terms') }}</a>
                <a href="#">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<button id="backToTop" title="Back to top"><i class="bi bi-arrow-up"></i></button>

<script>
    document.getElementById('themeToggle').addEventListener('click', function (e) {
        e.preventDefault();
        document.body.classList.toggle('bg-dark');
        document.body.classList.toggle('text-white');
    });
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', function () {
        backToTop.style.display = window.scrollY > 300 ? 'flex' : 'none';
    });
    backToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>

</body>
</html>