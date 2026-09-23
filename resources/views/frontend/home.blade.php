@extends('frontend.layouts.app')

@section('title', $categoryName ?? __('site.home'))

@section('content')

@php
    /*
        ---------------------------------------------------------------
        LAYOUT VALUES (must match Admin CategoryController::$layoutOptions)
            list | stat | big-grid | masonry | minimal | timeline | icon-card
            split | carousel | headline        <- 3 naya

        ASSUMED FIELDS (marked with a warning sign, adjust here only)
          News:     title, excerpt, slug, image, published_at, views,
                    category (relation), author (relation)
          Category: nameIn($locale), slug, accent_color, icon, layout_type
          Author:   name

        ROUTES
          news detail -> route('news.show', $item->slug)
          category    -> route('category.show', $slug)
          all news    -> route('news.latest')   (optional, button hides if missing)

        PAGINATION: hataisakiyo. Controller le ->take(9)->get() ya
        ->simplePaginate(9) dinu sakchha, duitai ma kaam garchha.
        ---------------------------------------------------------------
    */
    $locale = app()->getLocale();

    $placeholder = asset('images/placeholder-news.jpg');

    $defaultSectionLimit = 9;   // har category section ma kati news
    $minSectionItems     = 3;   // fresh news yo bhanda kam bhaye matra repeat le fill garcha

    $sidebarMax          = 5;   // sidebar ma trending pachhi kati category tab dekhaune
    $sidebarItemsPerBox  = 10;  // har tab ma maximum kati news (scroll garera herne)
    $sidebarMin          = 8;   // fresh news kam bhaye yo number samma purano le fill garcha

    $imgUrl = function ($item) use ($placeholder) {
        if (empty($item->image)) {
            return $placeholder;
        }
        if (str_starts_with($item->image, 'http')) {
            return $item->image;
        }
        return asset('storage/' . $item->image);
    };

    $catName = fn ($item) => $item->category?->nameIn($locale) ?? $item->category?->name ?? '';

    // minutes (integer). Carbon 3 ma diffInMinutes() float dincha, tesaile (int) + abs
    $minsSince = fn ($item) => $item->published_at
        ? (int) abs($item->published_at->diffInMinutes(now()))
        : 0;

    $timeAgo = function ($item) use ($minsSince) {
        $mins = $minsSince($item);
        if ($mins < 60) return __('site.mins_ago', ['n' => $mins]);
        $h = intdiv($mins, 60);
        if ($h < 24) return __('site.hours_ago', ['n' => $h]);
        return __('site.days_ago', ['n' => intdiv($h, 24)]);
    };

    $isFresh = fn ($item) => $item->published_at && $minsSince($item) < 15;

    $freshLabel = trans()->has('site.just_now')
        ? __('site.just_now')
        : __('site.mins_ago', ['n' => 0]);

    // paginator ra plain collection duitai support garne
    $newsItems   = method_exists($news, 'items') ? collect($news->items()) : collect($news);
    $onFirstPage = !method_exists($news, 'currentPage') || $news->currentPage() === 1;

    $usedSlugs = collect();

    if ($onFirstPage) {
        $heroMain  = $newsItems->first();
        $heroSide  = $newsItems->slice(1, 3)->values();
        $gridItems = $newsItems->slice(4)->values();

        if ($heroMain) {
            $usedSlugs->push($heroMain->slug);
        }
        $usedSlugs = $usedSlugs->merge($heroSide->pluck('slug'));
    } else {
        $heroMain  = null;
        $heroSide  = collect();
        $gridItems = $newsItems;
    }

    $usedSlugs = $usedSlugs->merge($gridItems->pluck('slug'));

    /* ---------- TRENDING ---------- */
    $trendingAll   = collect($trending ?? []);
    $trendingFresh = $trendingAll->reject(fn ($t) => $usedSlugs->contains($t->slug))->values();

    if ($trendingFresh->count() >= 8) {
        $trendingSpotlight = $trendingFresh->take(6)->values();
        $trendingSidebar   = $trendingFresh->slice(6)->values();
    } else {
        // kam news bhayo: spotlight hatayera sidebar ma matra (duplicate nahune)
        $trendingSpotlight = collect();
        $trendingSidebar   = $trendingFresh->isNotEmpty() ? $trendingFresh : $trendingAll->take(6);
    }

    $usedSlugs = $usedSlugs->merge($trendingSpotlight->pluck('slug'))
                            ->merge($trendingSidebar->pluck('slug'));

    /* ---------- CATEGORY SECTIONS (fresh pahila, repeat last ma) ---------- */
    $preparedSections = collect();

    if ($onFirstPage && !empty($categorySections)) {
        foreach ($categorySections as $section) {
            $secLimit = $section['limit'] ?? $defaultSectionLimit;

            $pool  = collect($section['items'] ?? [])->unique('slug')->values();
            $fresh = $pool->reject(fn ($i) => $usedSlugs->contains($i->slug))->values();

            if ($fresh->count() >= $minSectionItems) {
                $items = $fresh->take($secLimit)->values();
            } else {
                $backfill = $pool->reject(fn ($i) => $fresh->contains('slug', $i->slug))
                                 ->take($minSectionItems - $fresh->count());
                $items = $fresh->concat($backfill)->take($secLimit)->values();
            }

            if ($items->isEmpty()) {
                continue;
            }

            $usedSlugs = $usedSlugs->merge($items->pluck('slug'));

            $lead      = $items->first();
            $subwidget = $section['subwidget'] ?? null;

            $subItems = collect($subwidget['items'] ?? [])
                ->reject(fn ($i) => $usedSlugs->contains($i->slug))
                ->take(6)->values();
            $usedSlugs = $usedSlugs->merge($subItems->pluck('slug'));

            // sidebar box ko news: main section ma nadekhieka (fresh) pahila
            $sideFresh = $pool->reject(fn ($i) => $usedSlugs->contains($i->slug))
                              ->take($sidebarItemsPerBox)->values();

            if ($sideFresh->count() < $sidebarMin) {
                $sideFill  = $pool->reject(fn ($i) => $sideFresh->contains('slug', $i->slug))
                                  ->take($sidebarMin - $sideFresh->count());
                $sideItems = $sideFresh->concat($sideFill)->values();
            } else {
                $sideItems = $sideFresh;
            }

            $usedSlugs = $usedSlugs->merge($sideFresh->pluck('slug'));

            $leadCatSlug = $lead->category->slug ?? null;

            $preparedSections->push([
                'title'      => $section['title'] ?? null,
                'items'      => $items,
                'accent'     => $section['accent_color'] ?? ($lead->category->accent_color ?? null) ?? '#b81830',
                'icon'       => $section['icon'] ?? ($lead->category->icon ?? null) ?? 'bi-grid-3x3-gap-fill',
                'layout'     => $section['layout'] ?? ($lead->category->layout_type ?? null) ?? 'list',
                'subcats'    => $section['subcats'] ?? [],
                'subwidget'  => $subwidget,
                'subItems'   => $subItems,
                'sideItems'  => $sideItems,
                'viewAllUrl' => $section['view_all_url'] ?? ($leadCatSlug ? route('category.show', $leadCatSlug) : null),
            ]);
        }
    }
@endphp

{{-- ================= HERO (page 1 only) ================= --}}
@if ($heroMain)
    <div class="hero-full mb-3">
        <a href="{{ route('news.show', $heroMain->slug) }}" class="hero-main">
            <img src="{{ ($imgUrl)($heroMain) }}" class="hero-main-img" alt="{{ $heroMain->title }}"
                 loading="eager" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
            <div class="hero-glow"></div>
            <div class="hero-main-overlay">
                @if ($heroMain->category)
                    <span class="badge-category">{{ ($catName)($heroMain) }}</span>
                @endif
                <h2 class="hero-title">{{ $heroMain->title }}</h2>
                <div class="news-meta light">
                    @if ($heroMain->author)
                        <span class="meta-chip"><i class="bi bi-person-fill"></i> {{ $heroMain->author->name }}</span>
                    @endif
                    <span class="meta-chip"><i class="bi bi-clock-fill"></i> {{ ($timeAgo)($heroMain) }}</span>
                    @if (!empty($heroMain->views))
                        <span class="meta-chip"><i class="bi bi-eye-fill"></i> {{ number_format($heroMain->views) }} {{ __('site.views') }}</span>
                    @endif
                </div>
            </div>
        </a>
    </div>

    @if ($heroSide->isNotEmpty())
        <div class="hero-side-row mb-4">
            @foreach ($heroSide as $side)
                <a href="{{ route('news.show', $side->slug) }}" class="hero-side-item">
                    <div class="hero-side-header">
                        @if ($side->category)
                            <span class="badge-category small">{{ ($catName)($side) }}</span>
                        @endif
                        <h6>{{ Str::limit($side->title, 90) }}</h6>
                        <div class="hero-side-meta">
                            <span class="hero-side-avatar">{{ mb_substr($side->author->name ?? 'M', 0, 1) }}</span>
                            <span class="hero-side-author">{{ $side->author->name ?? 'JP News' }}</span>
                            <span class="meta-dot">•</span>
                            <span class="hero-side-time"><i class="bi bi-clock"></i> {{ ($timeAgo)($side) }}</span>
                        </div>
                    </div>
                    <div class="hero-side-img-wrap">
                        <img src="{{ ($imgUrl)($side) }}" alt="{{ $side->title }}"
                             loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="ad-banner-box mb-4">
        <span class="ad-label">{{ __('site.ad_space') }}</span>
        <div class="ad-banner-placeholder">
            <i class="bi bi-image"></i>
            <p>{{ __('site.ad_placeholder') }}</p>
        </div>
    </div>

    @if ($trendingSpotlight->isNotEmpty())
        <div class="spotlight-section mb-4">
            <h4 class="section-title"><i class="bi bi-fire"></i> {{ __('site.trending') }}</h4>
            <div class="spotlight-scroll-wrap">
                <div class="spotlight-track" id="spotlightTrack">
                    @foreach ($trendingSpotlight as $s)
                        <a href="{{ route('news.show', $s->slug) }}" class="spotlight-card">
                            <img src="{{ ($imgUrl)($s) }}" alt="{{ $s->title }}"
                                 loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                            <div class="spotlight-overlay">
                                <h6>{{ Str::limit($s->title, 60) }}</h6>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button type="button" class="spotlight-next"
                        onclick="document.getElementById('spotlightTrack').scrollBy({left: 300, behavior: 'smooth'})"
                        aria-label="Scroll">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    @endif
@endif

<div class="row">
    <div class="col-lg-8">

        {{-- ================= LATEST FEED ================= --}}
        <h4 class="section-title">{{ $categoryName ?? __('site.latest_news') }}</h4>

        <div class="news-feed">
            @forelse ($gridItems as $item)
                <a href="{{ route('news.show', $item->slug) }}" class="feed-item">
                    @if ($item->category)
                        <span class="badge-category small">{{ ($catName)($item) }}</span>
                    @endif
                    <h3>{{ $item->title }}</h3>
                    <div class="news-meta center">
                        @if ($item->author)
                            <span class="meta-chip"><i class="bi bi-person"></i> {{ $item->author->name }}</span>
                        @endif
                        <span class="meta-chip"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                        @if (($isFresh)($item))
                            <span class="meta-chip fresh"><i class="bi bi-lightning-fill"></i> {{ $freshLabel }}</span>
                        @endif
                    </div>
                    @if (!empty($item->image))
                        <div class="feed-item-img">
                            <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                 loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        </div>
                    @endif
                    @if (!empty($item->excerpt))
                        <p class="feed-item-caption">{{ Str::limit($item->excerpt, 170) }}</p>
                    @endif
                </a>
            @empty
                <p>{{ __('site.no_more_news') }}</p>
            @endforelse
        </div>

    </div>{{-- /col-lg-8 (feed) --}}

    {{-- ================= SIDEBAR (alag alag box, har box ko afnai fixed height + scroll) ================= --}}
    @php
        // box 1: trending, 10 samma (fresh pahila, kam bhaye trending bata fill)
        $trendingTab = $trendingSidebar
            ->concat($trendingAll->reject(fn ($t) => $trendingSidebar->contains('slug', $t->slug)))
            ->take(10)->values();

        // category box haru hataisakiyo — sidebar ma aba trending matra dekhinchha
    @endphp

    <div class="col-lg-4 sidebar-col">

        {{-- ===== box 1: बढी पढिएका ===== --}}
        <div class="sidebar-box side-box">
            <h5 class="sidebar-title">{{ __('site.trending') }}</h5>
            <ul class="trending-list-v2 side-scroll">
                @forelse ($trendingTab as $index => $t)
                    <li>
                        <span class="trend-num">{{ $index + 1 }}</span>
                        <div class="trend-body">
                            <a href="{{ route('news.show', $t->slug) }}">{{ Str::limit($t->title, 70) }}</a>
                            <div class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($t) }}</div>
                        </div>
                        <div class="trend-thumb">
                            <img src="{{ ($imgUrl)($t) }}" alt="{{ $t->title }}"
                                 loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        </div>
                    </li>
                @empty
                    <li class="trend-empty">{{ __('site.no_data') }}</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>


    <div class="row">
        <div class="col-12">

        {{-- ================= CATEGORY SECTIONS (pagination ko thau ma) ================= --}}
        @foreach ($preparedSections as $sec)
            @php
                $secItems   = $sec['items'];
                $secLead    = $secItems->first();
                $secRest    = $secItems->slice(1)->values();
                $secAccent  = $sec['accent'];
                $secIcon    = $sec['icon'];
                $layout     = $sec['layout'];
                $subcats    = $sec['subcats'];
                $subwidget  = $sec['subwidget'];
                $subItems   = $sec['subItems'];
                $viewAllUrl = $sec['viewAllUrl'];
            @endphp

            <div class="category-section" style="--cat-accent: {{ $secAccent }};">
                @if (!empty($sec['title']))
                    <div class="category-section-head">
                        <span class="category-icon-badge"><i class="bi {{ $secIcon }}"></i></span>
                        <h4 class="section-title cat-title">{{ $sec['title'] }}</h4>
                        @if ($sec['viewAllUrl'])
                            <a href="{{ $sec['viewAllUrl'] }}" class="view-all-link">
                                {{ trans()->has('site.see_all') ? __('site.see_all') : 'सबै हेर्नुहोस्' }}
                            </a>
                        @endif
                    </div>
                @endif

                @if (!empty($subcats))
                    <div class="cat-subnav">
                        @foreach ($subcats as $sc)
                            <a href="{{ $sc['url'] }}" class="cat-subnav-chip">{{ $sc['title'] }}</a>
                        @endforeach
                    </div>
                @endif

                @switch($layout)

                    @case('list')
                        <div class="cat-list-layout">
                            @foreach ($secItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="cat-list-row">
                                    <span class="cat-list-num">{{ $loop->iteration }}</span>
                                    <span class="cat-list-img">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </span>
                                    <span class="cat-list-body">
                                        <h5>{{ Str::limit($item->title, 75) }}</h5>
                                        @if (!empty($item->excerpt))
                                            <p>{{ Str::limit($item->excerpt, 100) }}</p>
                                        @endif
                                        <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        @break

                    @case('stat')
                        <div class="cat-stat-layout">
                            @foreach ($secItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="cat-stat-card">
                                    <span class="cat-stat-img">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </span>
                                    <span class="cat-stat-body">
                                        <h5>{{ Str::limit($item->title, 70) }}</h5>
                                        @if (!empty($item->excerpt))
                                            <p>{{ Str::limit($item->excerpt, 80) }}</p>
                                        @endif
                                        <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        @break

                    @case('big-grid')
                        <div class="cat-biggrid-layout">
                            @if ($secLead)
                                <a href="{{ route('news.show', $secLead->slug) }}" class="cat-biggrid-hero">
                                    <img src="{{ ($imgUrl)($secLead) }}" alt="{{ $secLead->title }}"
                                         loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    <span class="cat-biggrid-hero-overlay">
                                        <span class="cat-biggrid-hero-tag" style="background: {{ $secAccent }};">
                                            <i class="bi {{ $secIcon }}"></i> {{ __('site.latest_news') }}
                                        </span>
                                        <h3>{{ Str::limit($secLead->title, 90) }}</h3>
                                        @if (!empty($secLead->excerpt))
                                            <p>{{ Str::limit($secLead->excerpt, 130) }}</p>
                                        @endif
                                        <span class="news-meta light">{{ ($timeAgo)($secLead) }}</span>
                                    </span>
                                </a>
                            @endif
                            @if ($secRest->isNotEmpty())
                                <div class="cat-biggrid-grid">
                                    @foreach ($secRest as $item)
                                        <a href="{{ route('news.show', $item->slug) }}" class="cat-biggrid-card">
                                            <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                                 loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                            <span class="cat-biggrid-overlay">
                                                <h5>{{ Str::limit($item->title, 60) }}</h5>
                                                <span class="news-meta light">{{ ($timeAgo)($item) }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @break

                    @case('masonry')
                        <div class="cat-masonry-layout">
                            @foreach ($secItems as $index => $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="cat-masonry-card {{ $index % 3 == 0 ? 'tall' : '' }}">
                                    <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                         loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    <span class="cat-masonry-caption" style="background: linear-gradient(transparent, {{ $secAccent }}f2);">
                                        {{ Str::limit($item->title, 55) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        @break

                    @case('minimal')
                        <div class="cat-minimal-layout">
                            @foreach ($secItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="cat-minimal-card">
                                    <span class="cat-minimal-img">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                        <span class="cat-minimal-tag" style="background: {{ $secAccent }};"><i class="bi {{ $secIcon }}"></i></span>
                                    </span>
                                    <span class="cat-minimal-body">
                                        <h5>{{ Str::limit($item->title, 75) }}</h5>
                                        @if (!empty($item->excerpt))
                                            <p>{{ Str::limit($item->excerpt, 90) }}</p>
                                        @endif
                                        <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        @break

                    @case('timeline')
                        <ul class="cat-timeline">
                            @foreach ($secItems as $item)
                                <li class="cat-timeline-item">
                                    <span class="cat-timeline-dot"></span>
                                    <a href="{{ route('news.show', $item->slug) }}" class="cat-timeline-link">
                                        <span class="news-meta timeline-date">{{ $item->published_at?->format('F j, Y') }}</span>
                                        <h5>{{ Str::limit($item->title, 80) }}</h5>
                                        @if (!empty($item->excerpt))
                                            <p>{{ Str::limit($item->excerpt, 110) }}</p>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @break

                    @case('icon-card')
                        <div class="cat-iconcard-layout">
                            @foreach ($secItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="cat-iconcard">
                                    <span class="cat-iconcard-top" style="background: {{ $secAccent }};"><i class="bi {{ $secIcon }}"></i></span>
                                    <span class="cat-iconcard-img">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </span>
                                    <span class="cat-iconcard-body">
                                        <h5>{{ Str::limit($item->title, 70) }}</h5>
                                        @if (!empty($item->excerpt))
                                            <p>{{ Str::limit($item->excerpt, 80) }}</p>
                                        @endif
                                        <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        @break

                    @case('split')
                        <div class="cat-split-layout">
                            @if ($secLead)
                                <a href="{{ route('news.show', $secLead->slug) }}" class="cat-split-lead">
                                    <span class="cat-split-lead-img">
                                        <img src="{{ ($imgUrl)($secLead) }}" alt="{{ $secLead->title }}"
                                             loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </span>
                                    <span class="cat-split-lead-body">
                                        <h3>{{ Str::limit($secLead->title, 90) }}</h3>
                                        @if (!empty($secLead->excerpt))
                                            <p>{{ Str::limit($secLead->excerpt, 120) }}</p>
                                        @endif
                                        <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($secLead) }}</span>
                                    </span>
                                </a>
                            @endif
                            <div class="cat-split-side">
                                @foreach ($secRest as $item)
                                    <a href="{{ route('news.show', $item->slug) }}" class="cat-split-row">
                                        <h5>{{ Str::limit($item->title, 70) }}</h5>
                                        <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @break

                    @case('carousel')
                        <div class="cat-carousel">
                            @foreach ($secItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="cat-carousel-card">
                                    <span class="cat-carousel-img">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </span>
                                    <h5>{{ Str::limit($item->title, 60) }}</h5>
                                    <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                                </a>
                            @endforeach
                        </div>
                        @break

                    @case('headline')
                        <ul class="cat-headline-layout">
                            @foreach ($secItems as $item)
                                <li>
                                    <a href="{{ route('news.show', $item->slug) }}" class="cat-headline-row">
                                        <span class="cat-headline-dot"></span>
                                        <span class="cat-headline-title">{{ Str::limit($item->title, 90) }}</span>
                                        <span class="cat-headline-time">{{ ($timeAgo)($item) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @break

                    @default
                        <div class="cat-list-layout">
                            @foreach ($secItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="cat-list-row">
                                    <span class="cat-list-num">{{ $loop->iteration }}</span>
                                    <span class="cat-list-img">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </span>
                                    <span class="cat-list-body">
                                        <h5>{{ Str::limit($item->title, 75) }}</h5>
                                        <span class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>

                @endswitch

                @if ($subwidget && $subItems->isNotEmpty())
                    <div class="sub-widget">
                        @if (!empty($subwidget['title']))
                            <h6 class="sub-widget-title">{{ $subwidget['title'] }}</h6>
                        @endif
                        <div class="sub-widget-grid">
                            @foreach ($subItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="sub-widget-item">
                                    <span class="sub-widget-thumb">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </span>
                                    <span class="sub-widget-text">{{ Str::limit($item->title, 65) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        </div>
    </div>

@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700;8..60,800;8..60,900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --brand: #b81830;
        --brand-dark: #8c1224;
        --navy: #1c3a5e;
        --ink: #1a1a1a;
        --muted: #6b6b6b;
        --surface: #ffffff;
        --surface-soft: #f6f4f1;
        --border: #e6e1d8;
        --radius-lg: 14px;
        --radius-md: 9px;
        --shadow-sm: 0 1px 3px rgba(20,15,5,.06), 0 1px 2px rgba(20,15,5,.04);
        --shadow-md: 0 10px 24px -10px rgba(20,15,5,.16);
        --shadow-lg: 0 26px 50px -20px rgba(20,15,5,.26);
        --ease: cubic-bezier(.2,.7,.2,1);
    }

    a { transition: color .18s var(--ease); text-decoration: none; }
    a:focus-visible, button:focus-visible {
        outline: 2px solid var(--brand); outline-offset: 2px; border-radius: 4px;
    }

    .hero-main-img,
    .hero-side-item img,
    .spotlight-card img,
    .feed-item-img img,
    .trend-thumb img {
        display: block; width: 100%; height: 100%;
        object-fit: cover; object-position: center;
        background: var(--surface-soft);
        transition: transform .5s var(--ease);
    }

    .badge-category {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--brand); color: #fff; font-size: 10.5px; font-weight: 700;
        letter-spacing: .5px; text-transform: uppercase;
        padding: 4px 12px; border-radius: 3px; margin-bottom: 10px;
        box-shadow: 0 3px 8px rgba(184,24,48,.35);
    }
    .badge-category.small { font-size: 10px; padding: 3px 10px; }

    .news-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13.5px; color: var(--muted); margin-top: 6px; }
    .news-meta.light { color: rgba(255,255,255,.85); }
    .news-meta.tiny { font-size: 12.5px; gap: 6px; margin-top: 4px; }
    .meta-chip { display: inline-flex; align-items: center; gap: 5px; }

    .section-title {
        font-weight: 800; font-size: 21px; letter-spacing: .1px; position: relative;
        margin-bottom: 24px; padding-bottom: 14px; display: flex; align-items: center; gap: 9px;
    }
    .section-title i { color: var(--brand); }
    .section-title::after {
        content: ""; position: absolute; left: 0; bottom: 0; width: 44px; height: 3px;
        border-radius: 3px; background: linear-gradient(90deg, var(--brand), var(--brand-dark));
    }
    .section-title::before {
        content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 1px; background: var(--border);
    }

    /* ===== hero ===== */
    .hero-main {
        position: relative; display: block; width: 100%; border-radius: var(--radius-lg); overflow: hidden;
        aspect-ratio: 16 / 9; max-height: 560px; isolation: isolate; box-shadow: var(--shadow-lg);
    }
    .hero-main:hover .hero-main-img { transform: scale(1.035); }
    .hero-glow {
        position: absolute; inset: 0; z-index: 1;
        background: radial-gradient(circle at 20% 0%, rgba(184,24,48,.22), transparent 55%);
        pointer-events: none;
    }
    .hero-main-overlay {
        position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;
        background: linear-gradient(180deg, transparent, rgba(0,0,0,.55) 40%, rgba(0,0,0,.92) 100%);
        padding: 48px 34px 28px; color: #fff;
    }
    .hero-title { font-size: 34px; font-weight: 800; line-height: 1.28; margin: 10px 0 4px; color: #fff; letter-spacing: -.2px; }
    .hero-main:hover .hero-title { color: #f4c6c6; }

    .hero-side-row { display: flex; flex-direction: column; gap: 36px; }
    .hero-side-item { display: flex; flex-direction: column; }
    .hero-side-item:hover img { transform: scale(1.05); }

    /* text block: standalone, centered, separate from the image below it */
    .hero-side-header { text-align: center; padding: 0 8px; margin-bottom: 18px; }
    .hero-side-item h6 {
        font-size: 40px; font-weight: 800; line-height: 1.4; margin: 10px 0 16px; color: var(--ink);
    }
    .hero-side-item:hover h6 { color: var(--brand); }
    .hero-side-meta {
        display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap;
    }
    .hero-side-avatar {
        width: 26px; height: 26px; border-radius: 50%; background: var(--brand); color: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; flex-shrink: 0;
    }
    .hero-side-author { font-size: 13px; font-weight: 700; color: var(--ink); }
    .hero-side-meta .meta-dot { opacity: .5; font-size: 13px; }
    .hero-side-time { font-size: 13px; color: var(--muted); display: inline-flex; align-items: center; gap: 4px; }

    /* image: its own container below the text */
    .hero-side-img-wrap { width: 100%; aspect-ratio: 16 / 9; border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); }

    /* ===== spotlight ===== */
    .spotlight-scroll-wrap { position: relative; }
    .spotlight-track {
        display: flex; gap: 18px; overflow-x: auto; scroll-snap-type: x mandatory;
        scrollbar-width: none; padding: 2px 2px 8px;
    }
    .spotlight-track::-webkit-scrollbar { display: none; }
    .spotlight-card {
        position: relative; flex: 0 0 260px; aspect-ratio: 3 / 4; max-height: 340px; border-radius: var(--radius-md);
        overflow: hidden; scroll-snap-align: start; isolation: isolate; box-shadow: var(--shadow-md);
        transition: box-shadow .25s var(--ease);
    }
    .spotlight-card:hover { box-shadow: var(--shadow-lg); }
    .spotlight-card:hover img { transform: scale(1.06); }
    .spotlight-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(180deg, transparent, rgba(0,0,0,.85));
        padding: 34px 16px 14px; color: #fff;
    }
    .spotlight-overlay h6 { font-size: 16px; font-weight: 700; line-height: 1.4; margin: 0; }
    .spotlight-card:hover h6 { color: #f4c6c6; }
    .spotlight-next {
        position: absolute; top: 50%; right: -8px; transform: translateY(-50%);
        width: 46px; height: 46px; border-radius: 50%; border: none;
        background: #fff; color: var(--ink); box-shadow: var(--shadow-md);
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        transition: background .2s, color .2s, transform .2s;
    }
    .spotlight-next:hover { background: var(--brand); color: #fff; transform: translateY(-50%) scale(1.06); }

    /* ===== latest feed ===== */
    .news-feed { display: flex; flex-direction: column; }
    .feed-item {
        display: block; text-align: center; padding: 30px 0;
        border-bottom: 1px solid var(--border); color: var(--ink);
    }
    .feed-item:first-child { padding-top: 6px; }
    .feed-item:last-child { border-bottom: none; }
    .feed-item .badge-category.small { margin-bottom: 12px; }
    .feed-item h3 {
        font-family: 'Source Serif 4', Georgia, serif; font-weight: 800; font-size: 23px;
        line-height: 1.38; color: var(--ink); margin: 0 0 12px; letter-spacing: -.2px;
    }
    .feed-item:hover h3 { color: var(--brand); }
    .news-meta.center { justify-content: center; margin-bottom: 16px; }
    .meta-chip.fresh { color: #2ecc71; font-weight: 700; }
    .feed-item-img { width: 100%; aspect-ratio: 16 / 9; overflow: hidden; margin-bottom: 12px; border-radius: var(--radius-md); }
    .feed-item:hover .feed-item-img img { transform: scale(1.03); }
    .feed-item-caption {
        font-size: 14px; color: var(--muted); line-height: 1.7; margin: 0;
        max-width: 640px; margin-left: auto; margin-right: auto;
    }

    /* ===== category sections ===== */
    .category-section {
        margin-top: 48px; padding-left: 18px;
        border-left: 3px solid color-mix(in srgb, var(--cat-accent) 35%, transparent);
    }
    .category-section-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .category-icon-badge {
        flex-shrink: 0; width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: color-mix(in srgb, var(--cat-accent) 12%, #fff);
        color: var(--cat-accent); font-size: 17px;
    }
    .category-section-head .cat-title { margin-bottom: 0; padding-bottom: 0; flex: 1; }
    .category-section-head .cat-title::after,
    .category-section-head .cat-title::before { display: none; }
    .cat-title { color: var(--ink); }
    .view-all-link {
        flex-shrink: 0; font-size: 12.5px; font-weight: 700; white-space: nowrap;
        color: #fff; background: var(--brand);
        padding: 6px 14px; border-radius: 999px;
        transition: background .18s var(--ease);
    }
    .view-all-link:hover { background: #d43349; }

    .cat-subnav {
        display: flex; flex-wrap: nowrap; gap: 8px; margin-bottom: 20px;
        overflow-x: auto; scrollbar-width: none; padding-bottom: 2px;
    }
    .cat-subnav::-webkit-scrollbar { display: none; }
    .cat-subnav-chip {
        flex: 0 0 auto; font-size: 12.5px; font-weight: 700; color: var(--muted);
        background: var(--surface-soft); border: 1px solid var(--border);
        border-radius: 999px; padding: 5px 14px; white-space: nowrap;
        transition: background .18s var(--ease), color .18s var(--ease), border-color .18s var(--ease);
    }
    .cat-subnav-chip:hover {
        background: color-mix(in srgb, var(--cat-accent) 12%, #fff);
        color: var(--cat-accent); border-color: var(--cat-accent);
    }

    /* shared image behaviour inside category layouts */
    .cat-list-img img, .cat-stat-img img, .cat-biggrid-hero img, .cat-biggrid-card img,
    .cat-minimal-img img, .cat-iconcard-img img, .cat-split-lead-img img, .cat-carousel-img img {
        display: block; width: 100%; height: 100%; object-fit: cover;
        background: var(--surface-soft); transition: transform .35s var(--ease);
    }

    /* ===== list ===== */
    .cat-list-layout { display: flex; flex-direction: column; gap: 12px; }
    .cat-list-row {
        display: flex; align-items: center; gap: 16px; background: var(--surface);
        border: 1px solid var(--border); border-radius: var(--radius-md);
        padding: 14px; box-shadow: var(--shadow-sm); transition: box-shadow .2s var(--ease), transform .2s var(--ease);
    }
    .cat-list-row:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .cat-list-num { font-size: 24px; font-weight: 800; min-width: 30px; color: var(--cat-accent); opacity: .5; flex-shrink: 0; }
    .cat-list-img { flex-shrink: 0; width: 150px; aspect-ratio: 4/3; border-radius: 8px; overflow: hidden; }
    .cat-list-body h5 { font-size: 17px; font-weight: 700; margin: 0 0 4px; line-height: 1.4; color: var(--ink); }
    .cat-list-row:hover .cat-list-body h5 { color: var(--cat-accent); }
    .cat-list-body p { font-size: 13.5px; color: var(--muted); margin: 0 0 4px; line-height: 1.55; }

    /* ===== stat ===== */
    .cat-stat-layout { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
    .cat-stat-card {
        display: block; background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm);
        transition: box-shadow .2s var(--ease), transform .2s var(--ease);
    }
    .cat-stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .cat-stat-img { display: block; aspect-ratio: 16/10; overflow: hidden; }
    .cat-stat-card:hover .cat-stat-img img { transform: scale(1.05); }
    .cat-stat-body { display: block; padding: 14px; }
    .cat-stat-body h5 { font-size: 15.5px; font-weight: 700; margin: 0 0 6px; line-height: 1.4; color: var(--ink); }
    .cat-stat-card:hover .cat-stat-body h5 { color: var(--cat-accent); }
    .cat-stat-body p { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.5; }

    /* ===== big-grid ===== */
    .cat-biggrid-hero {
        display: block; position: relative; width: 100%; border-radius: var(--radius-lg);
        overflow: hidden; aspect-ratio: 16/9; max-height: 380px; margin-bottom: 16px; box-shadow: var(--shadow-md);
    }
    .cat-biggrid-hero:hover img { transform: scale(1.04); }
    .cat-biggrid-hero-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,.9));
        padding: 26px 24px 20px; display: block;
    }
    .cat-biggrid-hero-tag {
        display: inline-block; color: #fff; font-size: 11px; font-weight: 700;
        padding: 5px 13px; border-radius: 20px; margin-bottom: 10px;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .cat-biggrid-hero-overlay h3 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 6px; line-height: 1.32; }
    .cat-biggrid-hero-overlay p { color: #e4e4e4; font-size: 14px; margin: 0 0 6px; max-width: 640px; line-height: 1.5; }
    .cat-biggrid-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .cat-biggrid-card { display: block; position: relative; border-radius: 10px; overflow: hidden; aspect-ratio: 4/3; box-shadow: var(--shadow-sm); }
    .cat-biggrid-card:hover img { transform: scale(1.06); }
    .cat-biggrid-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,.88)); padding: 12px; display: block; }
    .cat-biggrid-overlay h5 { color: #fff; font-size: 14px; font-weight: 700; margin: 0 0 4px; line-height: 1.35; }

    /* ===== masonry ===== */
    .cat-masonry-layout { column-count: 2; column-gap: 14px; }
    .cat-masonry-card {
        display: block; break-inside: avoid; margin-bottom: 14px; position: relative;
        border-radius: 10px; overflow: hidden; box-shadow: var(--shadow-sm);
    }
    .cat-masonry-card img { width: 100%; display: block; height: 200px; object-fit: cover; transition: transform .35s var(--ease); }
    .cat-masonry-card.tall img { height: 300px; }
    .cat-masonry-card:hover img { transform: scale(1.05); }
    .cat-masonry-caption {
        position: absolute; left: 0; right: 0; bottom: 0;
        display: block; color: #fff; font-size: 14px; font-weight: 700; padding: 28px 12px 10px; line-height: 1.4;
    }

    /* ===== minimal ===== */
    .cat-minimal-layout { display: flex; flex-direction: column; gap: 2px; }
    .cat-minimal-card {
        display: flex; gap: 16px; align-items: flex-start; padding: 16px 0;
        border-bottom: 1px solid var(--border);
    }
    .cat-minimal-card:last-child { border-bottom: none; }
    .cat-minimal-img { position: relative; flex-shrink: 0; width: 140px; aspect-ratio: 4/3; border-radius: 8px; overflow: hidden; }
    .cat-minimal-card:hover .cat-minimal-img img { transform: scale(1.06); }
    .cat-minimal-tag {
        position: absolute; bottom: 6px; left: 6px; width: 24px; height: 24px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 11px;
        box-shadow: 0 2px 6px rgba(0,0,0,.25);
    }
    .cat-minimal-body h5 { font-size: 16px; font-weight: 700; margin: 0 0 4px; line-height: 1.4; color: var(--ink); }
    .cat-minimal-card:hover .cat-minimal-body h5 { color: var(--cat-accent); }
    .cat-minimal-body p { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.5; }

    /* ===== timeline ===== */
    .cat-timeline { list-style: none; margin: 0; padding: 0; position: relative; padding-left: 28px; }
    .cat-timeline::before { content: ""; position: absolute; left: 6px; top: 6px; bottom: 6px; width: 2px; background: var(--border); }
    .cat-timeline-item { position: relative; padding-bottom: 22px; }
    .cat-timeline-item:last-child { padding-bottom: 0; }
    .cat-timeline-dot {
        position: absolute; left: -28px; top: 5px; width: 13px; height: 13px; border-radius: 50%;
        background: var(--cat-accent); border: 3px solid var(--surface); box-shadow: 0 0 0 2px var(--cat-accent);
    }
    .cat-timeline-link .timeline-date { font-weight: 700; font-size: 11.5px; text-transform: uppercase; letter-spacing: .5px; color: var(--cat-accent); }
    .cat-timeline-link h5 { font-size: 16px; font-weight: 700; margin: 4px 0; color: var(--ink); line-height: 1.4; }
    .cat-timeline-link:hover h5 { color: var(--cat-accent); }
    .cat-timeline-link p { font-size: 13.5px; color: var(--muted); margin: 0 0 6px; line-height: 1.6; }

    /* ===== icon-card ===== */
    .cat-iconcard-layout { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
    .cat-iconcard {
        display: block; background: var(--surface); border-radius: 12px; overflow: hidden;
        position: relative; box-shadow: var(--shadow-sm); transition: box-shadow .2s var(--ease), transform .2s var(--ease);
    }
    .cat-iconcard:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .cat-iconcard-top {
        position: absolute; top: 12px; left: 12px; width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 17px;
        z-index: 2; box-shadow: 0 3px 8px rgba(0,0,0,.2);
    }
    .cat-iconcard-img { display: block; aspect-ratio: 16/10; overflow: hidden; position: relative; }
    .cat-iconcard:hover .cat-iconcard-img img { transform: scale(1.06); }
    .cat-iconcard-body { display: block; padding: 14px; }
    .cat-iconcard-body h5 { font-size: 15.5px; font-weight: 700; margin: 0 0 6px; line-height: 1.4; color: var(--ink); }
    .cat-iconcard:hover .cat-iconcard-body h5 { color: var(--cat-accent); }
    .cat-iconcard-body p { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.5; }

    /* ===== split ===== */
    .cat-split-layout { display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px; }
    .cat-split-lead { display: block; }
    .cat-split-lead-img { display: block; aspect-ratio: 16/10; border-radius: var(--radius-md); overflow: hidden; margin-bottom: 12px; }
    .cat-split-lead:hover img { transform: scale(1.04); }
    .cat-split-lead-body { display: block; }
    .cat-split-lead-body h3 { font-size: 21px; font-weight: 800; line-height: 1.35; margin: 0 0 6px; color: var(--ink); }
    .cat-split-lead:hover h3 { color: var(--cat-accent); }
    .cat-split-lead-body p { font-size: 14px; color: var(--muted); line-height: 1.6; margin: 0 0 4px; }
    .cat-split-side { display: flex; flex-direction: column; }
    .cat-split-row { display: block; padding: 12px 0; border-bottom: 1px solid var(--border); }
    .cat-split-row:first-child { padding-top: 0; }
    .cat-split-row:last-child { border-bottom: none; }
    .cat-split-row h5 { font-size: 15.5px; font-weight: 700; line-height: 1.4; margin: 0; color: var(--ink); }
    .cat-split-row:hover h5 { color: var(--cat-accent); }

    /* ===== carousel ===== */
    .cat-carousel { display: flex; gap: 16px; overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 8px; scrollbar-width: thin; }
    .cat-carousel-card { flex: 0 0 230px; scroll-snap-align: start; display: block; }
    .cat-carousel-img { display: block; aspect-ratio: 4/3; border-radius: var(--radius-md); overflow: hidden; margin-bottom: 8px; }
    .cat-carousel-card:hover img { transform: scale(1.06); }
    .cat-carousel-card h5 { font-size: 15px; font-weight: 700; line-height: 1.4; margin: 0; color: var(--ink); }
    .cat-carousel-card:hover h5 { color: var(--cat-accent); }

    /* ===== headline ===== */
    .cat-headline-layout { list-style: none; margin: 0; padding: 0; }
    .cat-headline-row { display: flex; align-items: baseline; gap: 10px; padding: 11px 0; border-bottom: 1px dashed var(--border); }
    .cat-headline-layout li:last-child .cat-headline-row { border-bottom: none; }
    .cat-headline-dot { flex-shrink: 0; width: 7px; height: 7px; border-radius: 50%; background: var(--cat-accent); transform: translateY(-2px); }
    .cat-headline-title { flex: 1; font-size: 15.5px; font-weight: 600; line-height: 1.45; color: var(--ink); }
    .cat-headline-row:hover .cat-headline-title { color: var(--cat-accent); }
    .cat-headline-time { flex-shrink: 0; font-size: 12px; color: var(--muted); }

    /* small secondary widget shared across layouts */
    .sub-widget { margin-top: 18px; background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 18px 20px 20px; }
    .sub-widget-title { font-size: 14px; font-weight: 800; color: var(--cat-accent); margin: 0 0 12px; }
    .sub-widget-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 20px; }
    .sub-widget-item { display: flex; align-items: center; gap: 10px; }
    .sub-widget-thumb { flex-shrink: 0; width: 40px; height: 40px; border-radius: 7px; overflow: hidden; background: var(--surface); }
    .sub-widget-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .sub-widget-text { font-size: 13.5px; font-weight: 600; color: var(--ink); line-height: 1.4; }
    .sub-widget-item:hover .sub-widget-text { color: var(--cat-accent); }
    .sub-widget-more { display: inline-flex; align-items: center; gap: 2px; margin-top: 14px; font-size: 12.5px; font-weight: 700; color: var(--muted); }
    .sub-widget-more:hover { color: var(--cat-accent); }

    /* page arrows (prev / next) */
    .page-arrows { display: flex; justify-content: flex-end; gap: 10px; margin: 36px 0 8px; }
    .page-arrow {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #e6eefb; color: #1f56c4; font-size: 15px;
        transition: background .2s var(--ease), color .2s var(--ease), transform .2s var(--ease);
    }
    .page-arrow:hover { background: #1f56c4; color: #fff; transform: scale(1.06); }

    /* ===== sidebar ===== */
    .sidebar-col { align-self: flex-start; position: sticky; top: 20px; }
    .sidebar-box { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; box-shadow: var(--shadow-sm); }
    .sidebar-title { font-weight: 800; font-size: 18px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--border); color: var(--navy); }

    .trending-list-v2 { list-style: none; margin: 0; padding: 0; }
    .trending-list-v2 li { display: flex; align-items: flex-start; gap: 14px; padding: 18px 0; border-bottom: 1px solid var(--border); }
    .trending-list-v2 li:last-child { border-bottom: none; }
    .trend-num { font-family: 'Source Serif 4', Georgia, serif; font-size: 30px; font-weight: 800; color: var(--brand); line-height: 1; min-width: 32px; flex-shrink: 0; }
    .trend-body { flex: 1; min-width: 0; }
    .trend-body a { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 15px; font-weight: 600; color: var(--ink); line-height: 1.45; }
    .trend-body a:hover { color: var(--brand); }
    .trend-thumb { width: 60px; height: 60px; border-radius: 8px; overflow: hidden; flex-shrink: 0; }
    .trending-list-v2 li:hover .trend-thumb img { transform: scale(1.1); }
    .trend-empty { color: var(--muted); font-size: 14px; padding: 8px 0; }

    /* sidebar scroll: thin scrollbar, box haru bich ma gap */
    .sidebar-col { scrollbar-width: thin; scrollbar-color: #d8d2c6 transparent; padding-right: 4px; }
    .sidebar-col::-webkit-scrollbar { width: 6px; }
    .sidebar-col::-webkit-scrollbar-thumb { background: #d8d2c6; border-radius: 6px; }
    .sidebar-box + .sidebar-box { margin-top: 20px; }

    /* ===== sidebar: alag alag box, har box ko afnai fixed height ra scroll ===== */
    .side-box { padding: 20px 22px 10px; }
    .side-box .sidebar-title { margin-bottom: 4px; }

    .side-cat { border-top: 3px solid var(--cat-accent); }
    .side-cat-head {
        display: flex; align-items: center; gap: 10px;
        padding-bottom: 14px; border-bottom: 1px solid var(--border);
    }
    .side-cat-icon {
        flex-shrink: 0; width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        background: color-mix(in srgb, var(--cat-accent) 12%, #fff);
        color: var(--cat-accent); font-size: 15px;
    }
    .side-cat-title { font-size: 18px; font-weight: 800; color: var(--navy); }
    a.side-cat-title:hover { color: var(--cat-accent); }
    .side-cat .trend-num { color: var(--cat-accent); }

    /* har box ko height fixed (max 470px). Bhitra ko news dherai bhayo bhane yehi box bhitra scroll huncha */
    .side-scroll {
        max-height: 470px; overflow-y: auto; padding-right: 8px;
        scrollbar-width: thin; scrollbar-color: #d8d2c6 transparent;
    }
    .side-scroll::-webkit-scrollbar { width: 6px; }
    .side-scroll::-webkit-scrollbar-thumb { background: #d8d2c6; border-radius: 6px; }
    .side-scroll li { padding: 16px 0; }

    /* ===== ad ===== */
    .ad-banner-box { border: 1px solid var(--border); border-radius: var(--radius-lg); background: var(--surface-soft); text-align: center; padding: 16px; display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .ad-label { font-size: 10.5px; color: #a49a86; text-transform: uppercase; letter-spacing: 1.8px; font-weight: 700; }
    .ad-banner-placeholder { width: 100%; max-width: 970px; min-height: 110px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #b3a996; gap: 4px; }
    .ad-banner-placeholder i { font-size: 24px; opacity: .65; }
    .ad-banner-placeholder p { font-size: 12.5px; margin: 0; }

    @media (max-width: 768px) {
        .hero-side-item h6 { font-size: 26px; }
        .hero-main { aspect-ratio: 4 / 3; max-height: 320px; }
        .spotlight-card { flex-basis: 200px; }
        .ad-banner-placeholder { min-height: 80px; }
        .feed-item { padding: 22px 0; }
        .feed-item h3 { font-size: 19px; }
        .feed-item-caption { font-size: 13px; }

        .category-section { margin-top: 30px; padding-left: 12px; }
        .cat-subnav-chip { font-size: 12px; padding: 4px 12px; }
        .sub-widget-grid { grid-template-columns: 1fr; }

        .cat-stat-layout, .cat-biggrid-grid, .cat-iconcard-layout, .cat-split-layout { grid-template-columns: 1fr; }
        .cat-masonry-layout { column-count: 1; }
        .cat-list-row { flex-direction: column; align-items: flex-start; }
        .cat-list-img { width: 100%; }
        .cat-minimal-img { width: 100%; }
        .cat-minimal-card { flex-direction: column; }
        .cat-biggrid-hero { max-height: 260px; }
        .cat-biggrid-hero-overlay h3 { font-size: 18px; }
        .cat-carousel-card { flex-basis: 190px; }
    }
</style>
@endpush