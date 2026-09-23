<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JP News')</title>
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

        /* ===== Sticky category navbar ===== */
        .category-nav {
            border-top: 1px solid #1b4c96;
            border-bottom: 1px solid #1b4c96;
            background: #2260bf;
            position: sticky;
            top: 0;
            z-index: 1030;
            padding: 0;
            transition: box-shadow .25s ease, padding .25s ease;
        }
        .category-nav.is-stuck {
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            padding: 2px 0;
        }
        .category-nav .container { position: relative; }
        .nav-mini-logo {
            position: absolute;
            left: 15px;
            top: 50%;
            display: flex;
            align-items: center;
            opacity: 0;
            transform: translateY(-50%) scale(.85);
            pointer-events: none;
            will-change: opacity, transform;
            transition: opacity .2s ease, transform .2s ease;
        }
        .category-nav.is-stuck .nav-mini-logo {
            opacity: 1;
            transform: translateY(-50%) scale(1);
            pointer-events: auto;
        }
        .nav-mini-logo img {
            height: 44px;
            width: 44px;
            object-fit: contain;
            border-radius: 50%;
            flex-shrink: 0;
            background: #fff;
            padding: 2px;
        }
        .nav-actions {
            position: absolute;
            right: 15px;
            top: 50%;
            display: flex;
            align-items: center;
            gap: 16px;
            opacity: 0;
            transform: translateY(-50%) scale(.85);
            pointer-events: none;
            will-change: opacity, transform;
            transition: opacity .2s ease, transform .2s ease;
        }
        .category-nav.is-stuck .nav-actions {
            opacity: 1;
            transform: translateY(-50%) scale(1);
            pointer-events: auto;
        }
        .nav-actions a { color: #fff; font-weight: 700; font-size: 19px; cursor: pointer; }
        .nav-actions a:hover { color: #ffd94d; }
        .nav-actions .lang-switch { font-size: 13px !important; font-weight: 800; border: 1.5px solid rgba(255,255,255,.6); border-radius: 20px; padding: 4px 12px; color: #fff !important; }
        .nav-actions .lang-switch:hover { border-color: #fff; background: #fff; color: #2260bf !important; }
        .category-nav .nav { justify-content: center; flex-wrap: wrap; }
        .category-nav .nav-link { color: #fff; font-weight: 800; font-size: 16px; padding: 12px 20px; }
        .category-nav .nav-link:hover, .category-nav .nav-link.active { color: #ffd94d; }

        .newsletter-strip { background: linear-gradient(90deg, #e30613, #b8040f); color: #fff; padding: 28px 0; }
        .newsletter-strip h5 { font-weight: 800; margin-bottom: 4px; }
        .newsletter-strip p { font-size: 14px; opacity: 0.9; margin-bottom: 0; }
        .newsletter-form input { border: none; border-radius: 4px 0 0 4px; padding: 10px 14px; width: 100%; outline: none; }
        .newsletter-form button { background: #111; color: #fff; border: none; border-radius: 0 4px 4px 0; padding: 10px 20px; font-weight: 700; white-space: nowrap; }
        .newsletter-form button:hover { background: #000; }
        .site-footer { background: #f4f4f4; color: #444; padding: 50px 0 0; }
        .site-footer h5 { color: #1a1a1a; font-weight: 700; margin-bottom: 20px; font-size: 16px; position: relative; padding-bottom: 10px; }
        .site-footer h5::after { content: ''; position: absolute; left: 0; bottom: 0; width: 34px; height: 3px; background: #e30613; }
        .site-footer a { color: #555; }
        .site-footer a:hover { color: #e30613; }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 11px; font-size: 14px; }
        .footer-links li i { color: #e30613; margin-right: 6px; font-size: 13px; }
        .footer-social a { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background: #fff; border: 1px solid #e2e2e2; border-radius: 50%; margin-right: 8px; font-size: 16px; transition: .2s; color: #555; }
        .footer-social a:hover { background: #e30613; border-color: #e30613; color: #fff; transform: translateY(-2px); }
        .footer-social--bottom a {
            width: 32px; height: 32px; font-size: 14px;
            background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
            color: #fff; margin-right: 6px;
        }
        .footer-social--bottom a:hover { background: #fff; color: #e30613; border-color: #fff; transform: translateY(-2px); }
        .footer-about p { font-size: 14px; color: #666; line-height: 1.8; }
        .footer-contact li { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; margin-bottom: 12px; color: #444; }
        .footer-contact i { color: #e30613; margin-top: 3px; }
        .footer-bottom-bar { background: linear-gradient(90deg, #e30613, #b8040f); margin-top: 40px; padding: 20px 0; }
        .footer-bottom { font-size: 13px; color: #fff; }
        .footer-bottom a { color: #fff; margin-left: 16px; opacity: 0.85; }
        .footer-bottom a:hover { opacity: 1; text-decoration: underline; }
        #backToTop { position: fixed; bottom: 24px; right: 24px; width: 46px; height: 46px; border-radius: 50%; background: #e30613; color: #fff; display: none; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 999; cursor: pointer; border: none; }
        #backToTop:hover { background: #b8040f; }

        /* Footer ad strip — auto-scrolling marquee (carousel/dots removed) */
        .footer-ad-strip { text-align: center; padding: 22px 0 8px; margin-top: 0; }
        .footer-ad-strip--inline { text-align: left; padding: 0; margin-top: 0; margin-left: 0; }
        .footer-ad-strip--inline .footer-ad-label { margin-bottom: 10px; }
        .footer-ad-label {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 10.5px; color: #999; text-transform: uppercase;
            letter-spacing: 1.4px; margin-bottom: 16px; font-weight: 700;
        }

        .footer-ad-scroll {
            max-width: 100%; width: 100%;
            overflow: hidden;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 10px 26px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.05);
            padding: 16px 0;
        }
        .footer-ad-scroll-track {
            display: inline-flex;
            align-items: center;
            width: max-content;
            animation: adScroll 22s linear infinite;
        }
        .footer-ad-scroll:hover .footer-ad-scroll-track { animation-play-state: paused; }
        .footer-ad-scroll-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 220px;
            height: 120px;
            margin: 0 16px;
            flex-shrink: 0;
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
        }
        .footer-ad-scroll-item img {
            max-width: 92%;
            max-height: 85%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }
        @keyframes adScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
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
                <img src="https://i.ibb.co/Jjnrcd62/jitesh-pradhan-production-png.png" alt="JP News">
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
                <a href="#" class="theme-toggle-btn" title="Dark Mode"><i class="bi bi-moon"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="site-date">
    {{ now()->format('l, F j, Y') }}
</div>

<nav class="category-nav" id="categoryNav">
    <div class="container">
        <div class="nav-mini-logo">
            <a href="{{ route('home') }}">
                {{-- 👇 Yesma pani upar ko jasto aafno logo ko link halnus (imgbb wala replace garda dubai thau ma update garnus) --}}
                <img src="https://i.ibb.co/Jjnrcd62/jitesh-pradhan-production-png.png" alt="JP News">
            </a>
        </div>
        <ul class="nav">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('site.latest_news') }}</a></li>
            @foreach($navCategories as $cat)
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('category/'.$cat->slug) ? 'active' : '' }}" href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a>
                </li>
            @endforeach
        </ul>
        <div class="nav-actions">
            @if(app()->getLocale() === 'ne')
                <a href="{{ $switchUrl }}" class="lang-switch" title="Switch to English">EN</a>
            @else
                <a href="{{ $switchUrl }}" class="lang-switch" title="{{ __('site.switch_to_nepali') }}">{{ __('site.nepali_label') }}</a>
            @endif
            <a href="#" title="Menu"><i class="bi bi-list fs-3"></i></a>
            <a href="#" title="Search"><i class="bi bi-search"></i></a>
            <a href="#" class="theme-toggle-btn" title="Dark Mode"><i class="bi bi-moon"></i></a>
        </div>
    </div>
</nav>

{{-- Navbar ad removed per request --}}

<div class="container my-4">
    @yield('content')
</div>

{{-- Newsletter strip removed per request --}}

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>JP News {{ __('site.about_us') }}</h5>
                <div class="footer-about">
                    <p>{{ __('site.about_text') }}</p>
                </div>

                @if(isset($footerAds) && $footerAds->count() > 0)
                <div class="footer-ad-strip footer-ad-strip--inline mt-2 ms-0">
                    <div class="footer-ad-scroll">
                        <div class="footer-ad-scroll-track">
                            @foreach($footerAds as $ad)
                                <a href="{{ $ad->link ?? '#' }}" target="_blank" rel="noopener" class="footer-ad-scroll-item">
                                    <img src="{{ asset('storage/'.$ad->image) }}" alt="{{ $ad->title }}">
                                </a>
                            @endforeach
                            {{-- duplicate set so the scroll loop looks seamless --}}
                            @foreach($footerAds as $ad)
                                <a href="{{ $ad->link ?? '#' }}" target="_blank" rel="noopener" class="footer-ad-scroll-item" aria-hidden="true">
                                    <img src="{{ asset('storage/'.$ad->image) }}" alt="{{ $ad->title }}">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>{{ __('site.news_categories') }}</h5>
                <ul class="footer-links">
                    @foreach($navCategories->take(5) as $cat)
                        <li><a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>{{ __('site.quick_links') }}</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                    <li><a href="#">{{ __('site.about_us') }}</a></li>
                    <li><a href="#">{{ __('site.advertise') }}</a></li>
                    <li><a href="#">{{ __('site.privacy_policy') }}</a></li>
                    <li><a href="#">{{ __('site.contact') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>{{ __('site.contact_address') }}</h5>
                <ul class="footer-links footer-contact">
                    <li><i class="bi bi-geo-alt-fill"></i> {{ __('site.address') }}</li>
                    <li><i class="bi bi-envelope-fill"></i> info@JP News.com</li>
                    <li><i class="bi bi-telephone-fill"></i> {{ __('site.phone') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom-bar">
        <div class="container footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>&copy; {{ date('Y') }} JP News. {{ __('site.rights_reserved') }}</div>
            <div class="footer-social footer-social--bottom">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-youtube"></i></a>
                <a href="#"><i class="bi bi-twitter-x"></i></a>
                <a href="#"><i class="bi bi-tiktok"></i></a>
            </div>
            <div>
                <a href="#">{{ __('site.privacy_policy') }}</a>
                <a href="#">{{ __('site.terms') }}</a>
                <a href="#">{{ __('site.sitemap') }}</a>
            </div>
        </div>
    </div>
</footer>

<button id="backToTop" title="Back to top"><i class="bi bi-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.theme-toggle-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            document.body.classList.toggle('bg-dark');
            document.body.classList.toggle('text-white');
        });
    });
    const backToTop = document.getElementById('backToTop');
    backToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Sticky navbar mini-logo + back-to-top — single rAF-throttled scroll
    // handler. Fixed pixel trigger (not a measured offsetTop) so it
    // shows up right after a small scroll, reliably, on any page length.
    const categoryNav = document.getElementById('categoryNav');
    const STICKY_TRIGGER = 80; // px scrolled before mini-logo/shadow kicks in

    let ticking = false;
    function onScrollTick() {
        const y = window.scrollY;
        categoryNav.classList.toggle('is-stuck', y > STICKY_TRIGGER);
        backToTop.style.display = y > 300 ? 'flex' : 'none';
        ticking = false;
    }
    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(onScrollTick);
            ticking = true;
        }
    }, { passive: true });
</script>

</body>
</html>