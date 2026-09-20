@extends('frontend.layouts.app')

@section('title', $categoryName ?? __('site.home'))

@section('content')

@php
    /*
        ---------------------------------------------------------------
        ASSUMED MODEL FIELDS — adjust the ones marked ⚠️ in ONE place.

          News:     title, excerpt ⚠️, slug, image ⚠️, published_at,
                     views, category (relation), author (relation)
          Category: nameIn($locale), slug, accent_color ⚠️, icon ⚠️
                     (a bootstrap-icons class e.g. "bi-cpu"),
                     layout_type ⚠️ (one of: lead-list | text-list | grid),
                     subcategories() ⚠️ relation -> [nameIn($locale), slug]
          Author:    name ⚠️

        ROUTE NAMES — adjust to match routes/web.php:
          news detail  -> route('news.show', $item->slug)      ⚠️
          category     -> route('category.show', $slug)        ⚠️

        TRANSLATION KEYS (resources/lang/{en,ne}/site.php):
          site.home, site.latest_news, site.news_suffix, site.trending,
          site.no_data, site.no_more_news, site.view_all,
          site.ad_space, site.ad_placeholder,
          site.mins_ago (:n), site.hours_ago (:n), site.days_ago (:n)

        AD POLICY (per latest request)
          Only ONE ad slot remains on the whole homepage: the single
          banner right under the hero, on page 1 only. Both sidebar ad
          boxes have been removed so the sidebar is 100% trending news.

        CATEGORY LAYOUT POLICY — "editorial" per-category look
          ("ajib deikhincha" fix: layout no longer auto-alternates by
          index — real portals like onlinekhabar hand-pick a layout per
          category, so we do the same via $category->layout_type.)

          $categorySections drives the category blocks below the main
          feed. Each entry carries:
            'layout'       => 'lead-list' | 'text-list' | 'grid'
                                falls back to $category->layout_type,
                                then to 'lead-list' — NEVER derived
                                from the section's index anymore.
            'accent_color' => '#2f7d46'   // ⚠️ falls back to category->accent_color, then brand red
            'icon'         => 'bi-cpu'    // ⚠️ falls back to category->icon, then a generic icon
            'limit'        => 8            // optional — how many items this block
                                shows (defaults to $defaultSectionLimit, 5 or 8
                                are typical). The lead item + the rest together
                                add up to this number, for every layout.
            'view_all_url' => route(...)   // optional — where "view all" goes.
                                Falls back to route('category.show', slug) so
                                clicking through always lands on the full
                                category page, which paginates via $news.
            'subcats'      => [['title' => 'अर्थनीति', 'url' => '...'], ...]  // optional chip nav

          Four distinct widgets (pick the one that fits the category,
          in the admin panel — see controller note below):
            - block-list : SOLID accent-colour block as the lead (no
                            photo — headline + excerpt in white text),
                            then a dense 2-column text-only grid below
                            it. This is onlinekhabar's real pattern for
                            News/Business-style sections — matches the
                            reference screenshot exactly.
            - lead-list  : one big lead PHOTO + a clean text-only
                            headline list underneath. Good when the
                            category actually has strong photos.
            - text-list  : NO images, no colour block — just a plain
                            single-column dense list of title + tag +
                            time. Good for low-visual sections.
            - grid       : 3-up photo grid, caption under image.
                            Good for image-heavy sections like
                            Entertainment/Photo Feature.

          Optional per-section extras (used by block-list, but any
          layout can carry them):
            'subcats'    => chip row under the title — subcategory
                            links (बिजनेस: अर्थनीति, पर्यटन...) OR plain
                            numbered pills (प्रदेश समाचार: १, २, ३...).
                            Renders as a single-row horizontal scroll,
                            same as onlinekhabar, never wraps.
            'subwidget'  => ['title' => 'कर्पोरेट', 'view_all_url' => ...,
                              'items' => [...News...]]
                            A small secondary card block under the main
                            list — grey box, tiny square thumb + title,
                            2-column, with a round "सबै" button. Matches
                            the कर्पोरेट block under बिजनेस.

          Each section keeps its own accent color on the title
          underline / lead block, its icon badge, and its card tag —
          so categories stay visually distinguishable even when two of
          them share the same layout type.

        IMAGE CONSISTENCY
          Every image container is sized with CSS aspect-ratio (not a
          fixed px height) + object-fit:cover, and every <img> carries
          onerror="this.src='...placeholder'" — so source photos of any
          resolution/orientation are auto-cropped to the same shape and
          the grid never looks "jumpy".

        DUPLICATE-NEWS GUARD (per "same news repeats everywhere")
          $usedSlugs collects every slug already shown on the page as we
          render top-to-bottom: hero main + hero side -> main feed lead
          + list -> trending (spotlight + sidebar) -> each category
          section. Every later block rejects anything already in
          $usedSlugs before rendering, so no single news item can show
          up twice on the same page load. Trending is also split into
          a "spotlight" slice and a "sidebar" slice so those two blocks
          don't just mirror the same list back at each other — swap the
          slice logic below if you'd rather have the controller send
          two genuinely different trending queries.
        ---------------------------------------------------------------
    */
    $locale = app()->getLocale();

    $placeholder = asset('images/placeholder-news.jpg'); // ⚠️

    // How many news items show per category block on the homepage before
    // the user has to click "view all" into the full category page.
    // Override per-section with $section['limit'] (see below). Typical: 5 or 8.
    $defaultSectionLimit = 8; // ⚠️ set to 5 if you want a tighter homepage

    $imgUrl = function ($item) use ($placeholder) {
        if (empty($item->image)) {
            return $placeholder;
        }
        if (str_starts_with($item->image, 'http')) {
            return $item->image;
        }
        return asset('storage/' . $item->image); // ⚠️
    };

    $catName = fn ($item) => $item->category?->nameIn($locale) ?? $item->category?->name ?? '';

    $timeAgo = function ($item) use ($locale) {
        $mins = $item->published_at ? $item->published_at->diffInMinutes(now()) : 0;
        if ($mins < 60) return __('site.mins_ago', ['n' => $mins]);
        $h = intdiv($mins, 60);
        if ($h < 24) return __('site.hours_ago', ['n' => $h]);
        return __('site.days_ago', ['n' => intdiv($h, 24)]);
    };

    // "Just published" badge — flags anything under 15 minutes old.
    $isFresh = fn ($item) => $item->published_at && $item->published_at->diffInMinutes(now()) < 15;

    $newsItems   = collect($news->items());
    $onFirstPage = $news->currentPage() === 1;

    // ---- duplicate guard: tracks every slug already shown, top to bottom ----
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

    // main feed (lead + rest) also claims its slugs before trending/sections run
    $usedSlugs = $usedSlugs->merge($gridItems->pluck('slug'));

    // ---- trending: strip anything already shown above, then split into
    //      a spotlight slice and a sidebar slice so the two blocks don't
    //      just repeat the same list at each other ----
    $trendingFresh    = collect($trending ?? [])->reject(fn ($t) => $usedSlugs->contains($t->slug))->values();
    $trendingSpotlight = $trendingFresh->take(6)->values();
    $trendingSidebar   = $trendingFresh->slice(6)->values();

    // if there weren't enough "fresh" trending items to fill the sidebar,
    // fall back to the spotlight items rather than showing an empty box
    if ($trendingSidebar->isEmpty()) {
        $trendingSidebar = $trendingSpotlight;
    }

    $usedSlugs = $usedSlugs->merge($trendingSpotlight->pluck('slug'))
                            ->merge($trendingSidebar->pluck('slug'));
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
                    <div class="hero-side-img-wrap">
                        <img src="{{ ($imgUrl)($side) }}" alt="{{ $side->title }}"
                             loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                    </div>
                    <div class="hero-side-body">
                        <h6>{{ Str::limit($side->title, 60) }}</h6>
                        <div class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($side) }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    {{-- ================= THE ONLY AD SLOT ON THE PAGE ================= --}}
    <div class="ad-banner-box mb-4">
        <span class="ad-label">{{ __('site.ad_space') }}</span>
        <div class="ad-banner-placeholder">
            <i class="bi bi-image"></i>
            <p>{{ __('site.ad_placeholder') }}</p>
        </div>
    </div>

    {{-- ================= TRENDING SPOTLIGHT SLIDER ================= --}}
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

        {{--
            ================= MAIN NEWS BLOCK (onlinekhabar mobile-exact) =================
            Pattern: EVERY item — title first (centered), meta below, then a full-width
            image with caption underneath if the item has one. This matches the reference
            screenshot exactly (not just a single lead item — every item in the feed).
        --}}
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
                            <span class="meta-chip fresh"><i class="bi bi-lightning-fill"></i> {{ __('site.mins_ago', ['n' => 0]) }}</span>
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

        {{-- ================= PAGINATION ================= --}}
        {{-- ⚠️ If unstyled: php artisan vendor:publish --tag=laravel-pagination
             then {{ $news->links('pagination::bootstrap-5') }} --}}
        <div class="mt-4">
            {{ $news->links() }}
        </div>

        {{--
            ================= CATEGORY SECTIONS (page 1 only) =================
            Each category gets its own accent color + icon + EDITORIALLY
            chosen layout (from $category->layout_type, set per category in
            the admin panel — not derived from the loop index anymore).

            Duplicate guard: every section's items are filtered against
            $usedSlugs (everything already shown in hero / main feed /
            trending / earlier sections) before rendering, and whatever's
            left is added back into $usedSlugs for the next section.
        --}}
        @if ($onFirstPage && !empty($categorySections))
            @foreach ($categorySections as $secIndex => $section)
                @php
                    // Cap this category to 5/8 (or whatever 'limit' says) so the
                    // homepage stays a preview — "view all" below takes the
                    // reader into the full, paginated category page.
                    $secLimit = $section['limit'] ?? $defaultSectionLimit;

                    $secItems = collect($section['items'] ?? [])
                        ->reject(fn ($i) => $usedSlugs->contains($i->slug))
                        ->take($secLimit)
                        ->values();

                    $usedSlugs = $usedSlugs->merge($secItems->pluck('slug'));

                    $secLead   = $secItems->first();
                    $secRest   = $secItems->slice(1)->values();
                    $secAccent = $section['accent_color'] ?? ($secLead->category->accent_color ?? null) ?? '#b81830'; // ⚠️
                    $secIcon   = $section['icon'] ?? ($secLead->category->icon ?? null) ?? 'bi-grid-3x3-gap-fill'; // ⚠️

                    // ---- EDITORIAL layout pick — never index-based anymore ----
                    $layout = $section['layout']
                        ?? ($secLead->category->layout_type ?? null) // ⚠️
                        ?? 'block-list';

                    $subcats   = $section['subcats'] ?? [];
                    $subwidget = $section['subwidget'] ?? null;

                    // "view all" -> full category page. That page reuses this
                    // same view with $categoryName + a category-filtered,
                    // paginated $news, so $news->links() already gives it
                    // real pagination — clicking through is how the reader
                    // sees everything beyond this 5/8-item preview.
                    $viewAllUrl = $section['view_all_url']
                        ?? (($secLead->category->slug ?? null) ? route('category.show', $secLead->category->slug) : null); // ⚠️
                @endphp

                @if ($secItems->isEmpty())
                    @continue
                @endif

                <div class="category-section" style="--cat-accent: {{ $secAccent }};">
                    @if (!empty($section['title']))
                        <div class="category-section-head">
                            <span class="category-icon-badge"><i class="bi {{ $secIcon }}"></i></span>
                            <h4 class="section-title cat-title">{{ $section['title'] }}</h4>
                            @if ($viewAllUrl)
                                <a href="{{ $viewAllUrl }}" class="view-all-link">{{ __('site.view_all') }} <i class="bi bi-chevron-right"></i></a>
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

                    @if ($layout === 'block-list')
                        {{-- ---- BLOCK-LIST: solid accent-colour lead block (no photo) +
                              dense 2-column text-only grid — onlinekhabar's real pattern
                              for समाचार / बिजनेस style sections ---- --}}
                        @if ($secLead)
                            <a href="{{ route('news.show', $secLead->slug) }}" class="block-lead">
                                <h3>{{ $secLead->title }}</h3>
                                @if (!empty($secLead->excerpt))
                                    <p>{{ Str::limit($secLead->excerpt, 140) }}</p>
                                @endif
                            </a>
                        @endif

                        @if ($secRest->isNotEmpty())
                            <ul class="text-grid cols-{{ $section['columns'] ?? 2 }}">
                                @foreach ($secRest as $item)
                                    <li>
                                        <a href="{{ route('news.show', $item->slug) }}">{{ Str::limit($item->title, 70) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($subwidget && !empty($subwidget['items']))
                            <div class="sub-widget">
                                @if (!empty($subwidget['title']))
                                    <h6 class="sub-widget-title">{{ $subwidget['title'] }}</h6>
                                @endif
                                <div class="sub-widget-grid">
                                    @foreach (collect($subwidget['items'])->take(6) as $item)
                                        <a href="{{ route('news.show', $item->slug) }}" class="sub-widget-item">
                                            <span class="sub-widget-thumb">
                                                <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                                     loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                            </span>
                                            <span class="sub-widget-text">{{ Str::limit($item->title, 65) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                                @if (!empty($subwidget['view_all_url']))
                                    <a href="{{ $subwidget['view_all_url'] }}" class="sub-widget-more">{{ __('site.view_all') }} <i class="bi bi-chevron-right"></i></a>
                                @endif
                            </div>
                        @endif

                    @elseif ($layout === 'grid')
                        {{-- ---- GRID: 3-up photo-report cards, caption UNDER image ---- --}}
                        <div class="news-grid">
                            @foreach ($secItems as $item)
                                <a href="{{ route('news.show', $item->slug) }}" class="news-grid-card">
                                    <div class="news-grid-img">
                                        <img src="{{ ($imgUrl)($item) }}" alt="{{ $item->title }}"
                                             loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </div>
                                    <h5>{{ Str::limit($item->title, 90) }}</h5>
                                    <div class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($item) }}</div>
                                </a>
                            @endforeach
                        </div>

                    @elseif ($layout === 'text-list')
                        {{-- ---- TEXT-LIST: no images at all — dense, editorial "wire" list.
                              Good for high-volume sections (Tech, Lifestyle) where a photo
                              per row just adds visual noise. ---- --}}
                        <ul class="headline-list wire">
                            @foreach ($secItems as $item)
                                <li>
                                    <a href="{{ route('news.show', $item->slug) }}">
                                        @if ($item->category)
                                            <span class="tag-label">{{ ($catName)($item) }}</span>
                                        @endif
                                        <span class="headline-text">{{ Str::limit($item->title, 85) }}</span>
                                        <span class="headline-time">{{ ($timeAgo)($item) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                    @else
                        {{-- ---- LEAD-LIST (default): one lead photo + text-only list ---- --}}
                        <div class="category-block">
                            @if ($secLead)
                                <a href="{{ route('news.show', $secLead->slug) }}" class="category-lead">
                                    <div class="category-lead-img">
                                        <img src="{{ ($imgUrl)($secLead) }}" alt="{{ $secLead->title }}"
                                             loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                    </div>
                                    <h5>{{ Str::limit($secLead->title, 80) }}</h5>
                                    <div class="news-meta tiny"><i class="bi bi-clock"></i> {{ ($timeAgo)($secLead) }}</div>
                                </a>
                            @endif

                            @if ($secRest->isNotEmpty())
                                <ul class="headline-list compact">
                                    @foreach ($secRest as $item)
                                        <li>
                                            <a href="{{ route('news.show', $item->slug) }}">
                                                <span class="headline-text">{{ Str::limit($item->title, 70) }}</span>
                                                <span class="headline-time">{{ ($timeAgo)($item) }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    {{-- ================= SIDEBAR (trending only — no ads here) ================= --}}
    <div class="col-lg-4 sidebar-col">
        <div class="sidebar-box">
            <h5 class="sidebar-title">{{ __('site.trending') }}</h5>
            <ul class="trending-list-v2">
                @forelse ($trendingSidebar as $index => $t)
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
    .lead-feature-img img,
    .feed-item-img img,
    .category-lead-img img,
    .news-grid-img img,
    .trend-thumb img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
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

    .tag-label {
        display: inline-block; font-size: 10.5px; font-weight: 700; color: var(--brand);
        text-transform: uppercase; letter-spacing: .4px; margin-right: 8px; flex-shrink: 0;
    }

    .news-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13.5px; color: var(--muted); margin-top: 6px; }
    .news-meta.light .meta-chip { color: rgba(255,255,255,.85); }
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

    .hero-main {
        position: relative; display: block; border-radius: var(--radius-lg); overflow: hidden;
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

    .hero-side-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .hero-side-item {
        display: flex; gap: 16px; background: var(--surface);
        border: 1px solid var(--border); border-radius: var(--radius-md);
        overflow: hidden; padding: 16px; box-shadow: var(--shadow-sm);
        transition: box-shadow .25s var(--ease), transform .25s var(--ease), border-color .2s;
    }
    .hero-side-item:hover { border-color: var(--brand); box-shadow: var(--shadow-md); transform: translateY(-3px); }
    .hero-side-item:hover img { transform: scale(1.08); }
    .hero-side-img-wrap { flex-shrink: 0; width: 150px; aspect-ratio: 4 / 3; border-radius: 8px; overflow: hidden; }
    .hero-side-item h6 { font-size: 17px; font-weight: 800; margin: 2px 0 8px; line-height: 1.38; color: var(--ink); }
    .hero-side-item .news-meta { font-size: 14px; }
    .hero-side-item:hover h6 { color: var(--brand); }

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

    .news-block { display: flex; flex-direction: column; }

    /* MAIN FEED — onlinekhabar-exact: every item is title -> centered meta ->
       full-width image -> caption, stacked one after another */
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
    .feed-item-img {
        width: 100%; aspect-ratio: 16 / 9; overflow: hidden; margin-bottom: 12px;
        border-radius: var(--radius-md);
    }
    .feed-item:hover .feed-item-img img { transform: scale(1.03); }
    .feed-item-caption {
        font-size: 14px; color: var(--muted); line-height: 1.7; margin: 0;
        max-width: 640px; margin-left: auto; margin-right: auto;
    }

    .lead-feature {
        display: block; background: var(--surface-soft);
        border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 4px;
        border: 1px solid var(--border); box-shadow: var(--shadow-md);
        transition: box-shadow .25s var(--ease);
    }
    .lead-feature:hover { box-shadow: var(--shadow-lg); }
    .lead-feature-img { width: 100%; aspect-ratio: 16 / 9; max-height: 300px; overflow: hidden; }
    .lead-feature:hover .lead-feature-img img { transform: scale(1.05); }
    .lead-feature-body { padding: 22px 26px 24px; }
    .lead-feature-title {
        font-family: 'Source Serif 4', Georgia, serif; font-weight: 800; font-size: 25px;
        line-height: 1.34; color: var(--ink); margin: 0 0 10px; letter-spacing: -.2px;
    }
    .lead-feature:hover .lead-feature-title { color: var(--brand); }
    .lead-feature-body p { font-size: 15px; color: #4a4a4a; margin-bottom: 4px; line-height: 1.6; }

    .headline-list { list-style: none; margin: 0; padding: 0; }
    .headline-list li {
        border-bottom: 1px solid var(--border);
        opacity: 0; animation: rowIn .5s var(--ease) forwards;
        animation-delay: calc(var(--stagger, 0) * 35ms);
    }
    .headline-list li:last-child { border-bottom: none; }
    .headline-list a {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 6px; color: var(--ink);
        border-radius: 8px; transition: background .18s var(--ease);
    }
    .headline-list a:hover { background: var(--surface-soft); }
    .headline-list a:hover .headline-text { color: var(--brand); }
    .headline-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 5px; }
    .headline-text {
        font-size: 16px; font-weight: 600; line-height: 1.45; color: var(--ink);
        transition: color .18s var(--ease);
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .headline-time {
        flex-shrink: 0; font-size: 12.5px; color: var(--muted); white-space: nowrap;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .headline-list.compact a { padding: 12px 4px; }
    .headline-list.compact .headline-text { font-size: 14.5px; font-weight: 500; -webkit-line-clamp: 1; }

    /* TEXT-LIST layout: dense, image-free "wire" list */
    .headline-list.wire a {
        padding: 12px 4px; gap: 10px; flex-wrap: nowrap;
    }
    .headline-list.wire .headline-text {
        flex: 1; font-size: 15px; font-weight: 600; -webkit-line-clamp: 1;
    }
    .headline-list.wire .tag-label { margin-right: 0; }

    .headline-thumb {
        position: relative; flex-shrink: 0; width: 92px; aspect-ratio: 4 / 3;
        border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-sm);
    }
    .headline-list a:hover .headline-thumb img { transform: scale(1.1); }
    .fresh-dot {
        position: absolute; top: 6px; left: 6px; width: 8px; height: 8px;
        border-radius: 50%; background: #2ecc71; box-shadow: 0 0 0 2px rgba(255,255,255,.9);
    }

    @keyframes rowIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .category-section {
        margin-top: 48px;
        padding-left: 18px;
        border-left: 3px solid color-mix(in srgb, var(--cat-accent) 35%, transparent);
    }
    .category-section-head {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 14px;
    }
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
        font-size: 13px; font-weight: 700; color: var(--muted); white-space: nowrap;
        display: inline-flex; align-items: center; gap: 2px;
    }
    .view-all-link:hover { color: var(--cat-accent); }

    /* Subcategory chip nav, onlinekhabar-style (e.g. बिजनेस -> अर्थनीति, पर्यटन, बैंक...
       or प्रदेश समाचार -> numbered province pills १,२,३...) — single row, scrolls, never wraps */
    .cat-subnav {
        display: flex; flex-wrap: nowrap; gap: 8px; margin-bottom: 20px;
        overflow-x: auto; scrollbar-width: none; padding-bottom: 2px;
    }
    .cat-subnav::-webkit-scrollbar { display: none; }
    .cat-subnav-chip {
        flex: 0 0 auto;
        font-size: 12.5px; font-weight: 700; color: var(--muted);
        background: var(--surface-soft); border: 1px solid var(--border);
        border-radius: 999px; padding: 5px 14px; white-space: nowrap;
        transition: background .18s var(--ease), color .18s var(--ease), border-color .18s var(--ease);
    }
    .cat-subnav-chip:hover,
    .cat-subnav-chip.active {
        background: color-mix(in srgb, var(--cat-accent) 12%, #fff);
        color: var(--cat-accent); border-color: var(--cat-accent);
    }

    /* BLOCK-LIST layout: solid accent-colour lead (no photo) + dense text grid */
    .block-lead {
        display: block; background: var(--cat-accent);
        border-radius: var(--radius-lg); padding: 26px 28px;
        margin-bottom: 22px; box-shadow: var(--shadow-md);
        transition: box-shadow .25s var(--ease), transform .25s var(--ease);
    }
    .block-lead:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
    .block-lead h3 {
        color: #fff; font-weight: 800; font-size: 22px; line-height: 1.36;
        margin: 0 0 8px; letter-spacing: -.2px;
    }
    .block-lead p {
        color: rgba(255,255,255,.88); font-size: 14px; line-height: 1.6; margin: 0;
    }

    .text-grid {
        list-style: none; margin: 0 0 8px; padding: 0;
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: 4px 28px;
    }
    .text-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
    .text-grid li {
        border-bottom: 1px solid var(--border);
    }
    .text-grid a {
        display: block; padding: 13px 2px; font-size: 14.5px; font-weight: 600;
        color: var(--ink); line-height: 1.5;
    }
    .text-grid a:hover { color: var(--cat-accent); }

    /* Small secondary widget under a block-list section (e.g. कर्पोरेट under बिजनेस) */
    .sub-widget {
        margin-top: 18px; background: var(--surface-soft); border: 1px solid var(--border);
        border-radius: var(--radius-md); padding: 18px 20px 20px;
    }
    .sub-widget-title { font-size: 14px; font-weight: 800; color: var(--cat-accent); margin: 0 0 12px; }
    .sub-widget-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 20px; }
    .sub-widget-item { display: flex; align-items: center; gap: 10px; }
    .sub-widget-thumb {
        flex-shrink: 0; width: 40px; height: 40px; border-radius: 7px; overflow: hidden;
        background: var(--surface);
    }
    .sub-widget-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .sub-widget-text { font-size: 13.5px; font-weight: 600; color: var(--ink); line-height: 1.4; }
    .sub-widget-item:hover .sub-widget-text { color: var(--cat-accent); }
    .sub-widget-more {
        display: inline-flex; align-items: center; gap: 2px; margin-top: 14px;
        font-size: 12.5px; font-weight: 700; color: var(--muted);
    }
    .sub-widget-more:hover { color: var(--cat-accent); }

    .category-block {
         display: grid; grid-template-columns: minmax(180px, 240px) 1fr; gap: 20px;
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 20px; box-shadow: var(--shadow-sm);
    }
    .category-lead { display: block; }
    .category-lead-img { width: 100%; aspect-ratio: 16 / 10; border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); }
    .category-lead:hover .category-lead-img img { transform: scale(1.08); }
    .category-lead h5 { font-size: 16px; font-weight: 700; margin: 12px 0 2px; line-height: 1.42; color: var(--ink); }
    .category-lead:hover h5 { color: var(--cat-accent); }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px 24px;
    }
    .news-grid-card { display: block; }
    .news-grid-img {
        width: 100%;
        aspect-ratio: 16 / 10;
        border-radius: var(--radius-md);
        overflow: hidden;
        margin-bottom: 14px;
        box-shadow: var(--shadow-sm);
    }
    .news-grid-card:hover .news-grid-img img { transform: scale(1.05); }
    .news-grid-card h5 {
        font-size: 16.5px;
        font-weight: 700;
        line-height: 1.5;
        color: var(--ink);
        margin: 0;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .news-grid-card:hover h5 { color: var(--cat-accent); }
    .news-grid-card .news-meta.tiny { margin-top: 6px; }

    .sidebar-col {
        align-self: flex-start;
        position: sticky;
        top: 20px;
    }
    @media (min-width: 992px) {
        .sidebar-col { top: 20px; max-height: calc(100vh - 40px); overflow-y: auto; }
    }
    .sidebar-box {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 22px; box-shadow: var(--shadow-sm);
    }
    .sidebar-title {
        font-weight: 800; font-size: 18px; margin-bottom: 18px; padding-bottom: 14px;
        border-bottom: 1px solid var(--border); color: var(--navy);
    }

    .trending-list-v2 { list-style: none; margin: 0; padding: 0; }
    .trending-list-v2 li {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 0;
        border-bottom: 1px solid var(--border);
    }
    .trending-list-v2 li:last-child { border-bottom: none; }
    .trend-num {
        font-family: 'Source Serif 4', Georgia, serif;
        font-size: 30px;
        font-weight: 800;
        color: var(--brand);
        line-height: 1;
        min-width: 32px;
        flex-shrink: 0;
    }
    .trend-body { flex: 1; min-width: 0; }
    .trend-body a {
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        font-size: 15px;
        font-weight: 600;
        color: var(--ink);
        line-height: 1.45;
    }
    .trend-body a:hover { color: var(--brand); }
    .trend-thumb {
        width: 60px; height: 60px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .trending-list-v2 li:hover .trend-thumb img { transform: scale(1.1); }
    .trend-empty { color: var(--muted); font-size: 14px; padding: 8px 0; }

    .ad-banner-box {
        border: 1px solid var(--border); border-radius: var(--radius-lg);
        background: var(--surface-soft); text-align: center;
        padding: 16px; display: flex; flex-direction: column; align-items: center; gap: 6px;
    }
    .ad-label { font-size: 10.5px; color: #a49a86; text-transform: uppercase; letter-spacing: 1.8px; font-weight: 700; }
    .ad-banner-placeholder {
        width: 100%; max-width: 970px; min-height: 110px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        color: #b3a996; gap: 4px;
    }
    .ad-banner-placeholder i { font-size: 24px; opacity: .65; }
    .ad-banner-placeholder p { font-size: 12.5px; margin: 0; }

    @media (max-width: 768px) {
        .hero-main { aspect-ratio: 4 / 3; max-height: 320px; }
        .hero-side-row { grid-template-columns: 1fr; }
        .spotlight-card { flex-basis: 200px; }
        .lead-feature-img { aspect-ratio: 4 / 3; }
        .ad-banner-placeholder { min-height: 80px; }
        .headline-thumb { width: 68px; }
        .headline-text { font-size: 14.5px; -webkit-line-clamp: 2; }
        .feed-item { padding: 22px 0; }
        .feed-item h3 { font-size: 19px; }
        .feed-item-caption { font-size: 13px; }

        .category-section { margin-top: 30px; padding-left: 12px; }
        .news-grid { grid-template-columns: repeat(2, 1fr); gap: 20px 16px; }
        .cat-subnav-chip { font-size: 12px; padding: 4px 12px; }
        .text-grid, .text-grid.cols-3 { grid-template-columns: 1fr; }
        .block-lead { padding: 20px; }
        .block-lead h3 { font-size: 19px; }
        .sub-widget-grid { grid-template-columns: 1fr; }

        .category-block {
            grid-template-columns: 1fr;
            padding: 0; overflow: hidden; gap: 0;
        }
        .category-lead-img { aspect-ratio: 16 / 9; border-radius: 0; }
        .category-lead h5 { padding: 0 14px; margin: 12px 0 2px; }
        .category-lead .news-meta.tiny { padding: 0 14px 14px; margin-top: 0; }
        .category-block .headline-list.compact { padding: 4px 14px 12px; }
    }
</style>
@endpush