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
        .site-header { padding: 20px 0 8px; }
        .social-icons { display: flex; align-items: center; gap: 16px; }
        .social-icons a { color: #333; font-size: 20px; }
        .social-icons a:hover { color: #e30613; }
        .site-logo { text-align: center; }
        .site-logo img { height: 55px; width: auto; max-width: 220px; object-fit: contain; }
        .site-logo .brand-tagline { font-size: 12px; color: #777; margin-top: 4px; }
        .header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 20px; }
        .header-actions a { color: #333; font-size: 20px; cursor: pointer; }
        .header-actions a:hover { color: #e30613; }
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
    <div class="breaking-badge">🔴 ब्रेकिङ</div>
    <div class="breaking-ticker">
        <div class="breaking-ticker-track">
            @forelse($breakingNews as $item)
                <span>{{ $item->title }}</span>
            @empty
                <span>ताजा समाचारका लागि हाम्रो साइटमा भ्रमण गर्नुहोस्</span>
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
                <img src="https://i.ibb.co/jvRxptRz/jitesh-pradhan-production-png.png" alt="MeroNews">
                <p class="brand-tagline">तपाईंको भरपर्दो समाचार स्रोत</p>
            </a>
        </div>
        <div class="col-4">
            <div class="header-actions">
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
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">ताजा समाचार</a></li>
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
                <h5>ताजा समाचार सीधा तपाईंको इमेलमा पाउनुहोस्</h5>
                <p>हरेक दिन बिहान महत्वपूर्ण समाचार सारांश पाउन सब्स्क्राइब गर्नुहोस्</p>
            </div>
            <div class="col-md-6">
                <form class="d-flex newsletter-form" onsubmit="return false;">
                    <input type="email" placeholder="तपाईंको इमेल राख्नुहोस्" required>
                    <button type="submit">सब्स्क्राइब</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>MeroNews बारे</h5>
                <div class="footer-about">
                    <p>नेपालको भरपर्दो अनलाइन समाचार पोर्टल। हामी राजनीति, अर्थतन्त्र, खेलकुद, मनोरञ्जन र प्रविधिसम्बन्धी ताजा र विश्वसनीय समाचार २४ घण्टै प्रकाशित गर्दछौं।</p>
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
                <h5>समाचार श्रेणी</h5>
                <ul class="footer-links">
                    @foreach($navCategories->take(5) as $cat)
                        <li><i class="bi bi-chevron-right"></i><a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>छिटो लिङ्क</h5>
                <ul class="footer-links">
                    <li><i class="bi bi-chevron-right"></i><a href="{{ route('home') }}">गृहपृष्ठ</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">हाम्रो बारे</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">विज्ञापन</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">गोपनीयता नीति</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="#">सम्पर्क</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <h5>सम्पर्क ठेगाना</h5>
                <ul class="footer-links footer-contact">
                    <li><i class="bi bi-geo-alt-fill"></i> काठमाडौं, नेपाल</li>
                    <li><i class="bi bi-envelope-fill"></i> info@meronews.com</li>
                    <li><i class="bi bi-telephone-fill"></i> ०१-४xxxxxx</li>
                </ul>
                <h5 class="mt-4">एप डाउनलोड गर्नुहोस्</h5>
                <div class="app-badges">
                    <a href="#"><i class="bi bi-google-play"></i> Google Play</a>
                    <a href="#"><i class="bi bi-apple"></i> App Store</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center">
            <div>&copy; {{ date('Y') }} MeroNews. सर्वाधिकार सुरक्षित।</div>
            <div>
                <a href="#">गोपनीयता नीति</a>
                <a href="#">सर्तहरू</a>
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