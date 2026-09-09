@extends('frontend.layouts.app')

@section('title', $categoryName)

@section('content')

<div class="cat-banner" style="background: linear-gradient(135deg, {{ $accent }}, {{ $accent }}cc);">
    <div class="cat-banner-icon"><i class="bi {{ $icon }}"></i></div>
    <div>
        <h2>{{ $categoryName }}</h2>
        <span class="cat-banner-sub">{{ __('site.fresh_updates') }}</span>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">

        @switch($layout)

            {{-- ===================== LIST LAYOUT (राजनीति) ===================== --}}
            @case('list')
                <div class="list-layout">
                    @forelse($news as $item)
                        <div class="list-row">
                            <div class="list-row-num" style="color: {{ $accent }};">{{ $loop->iteration }}</div>
                            <div class="list-row-img">
                                <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/160x120?text=No+Image' }}" loading="lazy">
                            </div>
                            <div class="list-row-body">
                                <h5><a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a></h5>
                                <p>{{ Str::limit($item->excerpt, 100) }}</p>
                                <div class="news-meta"><i class="bi bi-person"></i> {{ $item->author->name ?? 'MeroNews' }} <span class="dot">•</span> {{ $item->published_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>{{ __('site.no_news') }}</p></div>
                    @endforelse
                </div>
                @break

            {{-- ===================== STAT LAYOUT (अर्थतन्त्र) ===================== --}}
            @case('stat')
                <div class="stat-layout">
                    @forelse($news as $item)
                        <div class="stat-card">
                            <div class="stat-card-img">
                                <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/400x260?text=No+Image' }}" loading="lazy">
                                <div class="stat-badge" style="background: {{ $accent }};"><i class="bi bi-eye"></i> {{ $item->views }}</div>
                            </div>
                            <div class="stat-card-body">
                                <h5><a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a></h5>
                                <p>{{ Str::limit($item->excerpt, 80) }}</p>
                                <div class="news-meta">{{ $item->published_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>{{ __('site.no_news') }}</p></div>
                    @endforelse
                </div>
                @break

            {{-- ===================== BIG-GRID LAYOUT (खेलकुद) ===================== --}}
            @case('big-grid')
                <div class="biggrid-layout">
                    @forelse($news as $index => $item)
                        @if($index === 0)
                            <div class="biggrid-hero">
                                <a href="{{ route('news.show', $item->slug) }}">
                                    <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/900x480?text=No+Image' }}" loading="lazy">
                                    <div class="biggrid-hero-overlay">
                                        <span class="biggrid-hero-tag" style="background: {{ $accent }};"><i class="bi {{ $icon }}"></i> {{ __('site.latest_news') }}</span>
                                        <h3>{{ $item->title }}</h3>
                                        <p>{{ Str::limit($item->excerpt, 130) }}</p>
                                        <div class="news-meta light">{{ $item->published_at?->diffForHumans() }} <span class="dot">•</span> {{ $item->views }} {{ __('site.times_suffix') }}</div>
                                    </div>
                                </a>
                            </div>
                            <div class="biggrid-grid">
                        @endif

                        @if($index > 0)
                            <div class="biggrid-card">
                                <a href="{{ route('news.show', $item->slug) }}">
                                    <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/500x320?text=No+Image' }}" loading="lazy">
                                    <div class="biggrid-overlay">
                                        <h5>{{ $item->title }}</h5>
                                        <div class="news-meta light">{{ $item->published_at?->diffForHumans() }} <span class="dot">•</span> {{ $item->views }} {{ __('site.times_suffix') }}</div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if($loop->last)
                            </div>
                        @endif
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>{{ __('site.no_news') }}</p></div>
                    @endforelse
                </div>
                @break

            {{-- ===================== MASONRY LAYOUT (मनोरञ्जन) ===================== --}}
            @case('masonry')
                <div class="masonry-layout">
                    @forelse($news as $index => $item)
                        <div class="masonry-card {{ $index % 3 == 0 ? 'tall' : '' }}">
                            <a href="{{ route('news.show', $item->slug) }}">
                                <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/400x400?text=No+Image' }}" loading="lazy">
                                <div class="masonry-caption" style="background: linear-gradient(transparent, {{ $accent }}f2);">{{ Str::limit($item->title, 55) }}</div>
                            </a>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>{{ __('site.no_news') }}</p></div>
                    @endforelse
                </div>
                @break

            {{-- ===================== MINIMAL LAYOUT (प्रविधि) — image-based ===================== --}}
            @case('minimal')
                <div class="minimal-layout">
                    @forelse($news as $item)
                        <div class="minimal-card">
                            <div class="minimal-img">
                                <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/200x150?text=No+Image' }}" loading="lazy">
                                <span class="minimal-tag" style="background: {{ $accent }};"><i class="bi {{ $icon }}"></i></span>
                            </div>
                            <div class="minimal-body">
                                <h5><a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a></h5>
                                <p>{{ Str::limit($item->excerpt, 90) }}</p>
                                <div class="news-meta">{{ $item->published_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>{{ __('site.no_news') }}</p></div>
                    @endforelse
                </div>
                @break

            {{-- ===================== TIMELINE LAYOUT (विश्व) ===================== --}}
            @case('timeline')
                <div class="timeline-layout">
                    @forelse($news as $item)
                        <div class="timeline-item">
                            <div class="timeline-dot" style="background: {{ $accent }};"></div>
                            <div class="timeline-content">
                                <div class="news-meta timeline-date" style="color: {{ $accent }};">{{ $item->published_at?->format('F j, Y') }}</div>
                                <h5><a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a></h5>
                                <p>{{ Str::limit($item->excerpt, 100) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>{{ __('site.no_news') }}</p></div>
                    @endforelse
                </div>
                @break

            {{-- ===================== ICON-CARD LAYOUT (स्वास्थ्य) ===================== --}}
            @case('icon-card')
                <div class="iconcard-layout">
                    @forelse($news as $item)
                        <div class="iconcard">
                            <div class="iconcard-top" style="background: {{ $accent }};">
                                <i class="bi {{ $icon }}"></i>
                            </div>
                            <div class="iconcard-img">
                                <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/400x220?text=No+Image' }}" loading="lazy">
                            </div>
                            <div class="iconcard-body">
                                <h5><a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a></h5>
                                <p>{{ Str::limit($item->excerpt, 80) }}</p>
                                <div class="news-meta">{{ $item->published_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>{{ __('site.no_news') }}</p></div>
                    @endforelse
                </div>
                @break

        @endswitch

        <div class="d-flex justify-content-center mt-4">
            {{ $news->links() }}
        </div>
    </div>

    <div class="col-lg-4">
        <div class="sidebar-box mb-4">
            <h5 class="sidebar-title" style="border-color: {{ $accent }};"><i class="bi bi-fire" style="color: {{ $accent }};"></i> {{ __('site.trending') }}</h5>
            <ul class="trending-list">
                @forelse($trending as $index => $t)
                    <li>
                        <span class="trending-number" style="color: {{ $accent }};">{{ $index + 1 }}</span>
                        <div>
                            <a href="{{ route('news.show', $t->slug) }}" class="trending-title">{{ $t->title }}</a>
                            <div class="news-meta"><i class="bi bi-eye"></i> {{ $t->views }} {{ __('site.times_suffix') }}</div>
                        </div>
                    </li>
                @empty
                    <li class="trending-empty">{{ __('site.no_data') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="sidebar-ad-box">
            <span class="ad-label">{{ __('site.ad_space') }}</span>
            <div class="ad-placeholder">
                <i class="bi bi-image"></i>
                <p>{{ __('site.ad_placeholder') }}</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    :root {
        --text-dark: #16181c;
        --text-muted: #767e8c;
        --border-soft: #edeef1;
        --card-radius: 10px;
        --shadow-sm: 0 1px 3px rgba(20,20,30,0.06);
        --shadow-md: 0 6px 18px rgba(20,20,30,0.08);
        --transition: all .25s ease;
    }

    * { box-sizing: border-box; }

    .news-meta { font-size: 14px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 5px; }
    .news-meta.light { color: rgba(255,255,255,0.85); }
    .news-meta .dot { opacity: .5; }

    /* ===== Category banner ===== */
    .cat-banner { border-radius: 12px; padding: 28px 32px; color: #fff; display: flex; align-items: center; gap: 18px; box-shadow: var(--shadow-md); }
    .cat-banner-icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .cat-banner-icon i { font-size: 28px; }
    .cat-banner h2 { font-weight: 800; margin: 0; font-size: 28px; letter-spacing: -0.3px; }
    .cat-banner-sub { font-size: 13px; opacity: .85; }

    /* ===== Empty state ===== */
    .empty-state { text-align: center; padding: 50px 20px; color: var(--text-muted); }
    .empty-state i { font-size: 34px; opacity: .4; margin-bottom: 8px; display: block; }

    /* ===== Sidebar (shared) ===== */
    .sidebar-box { background: #fff; border: 1px solid var(--border-soft); border-radius: var(--card-radius); padding: 20px; box-shadow: var(--shadow-sm); }
    .sidebar-title { font-weight: 800; font-size: 16px; margin-bottom: 16px; border-bottom: 3px solid; padding-bottom: 10px; display: inline-flex; align-items: center; gap: 8px; }
    .trending-list { list-style: none; padding: 0; margin: 0; }
    .trending-list li { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border-soft); }
    .trending-list li:last-child { border-bottom: none; padding-bottom: 0; }
    .trending-list li:first-child { padding-top: 0; }
    .trending-number { font-weight: 800; font-size: 20px; min-width: 24px; opacity: .85; }
    .trending-title { font-size: 15px; font-weight: 600; color: var(--text-dark); line-height: 1.45; transition: var(--transition); }
    .trending-title:hover { color: #e30613; }
    .trending-empty { color: var(--text-muted); font-size: 13px; }
    .sidebar-ad-box { border: 1px dashed #d5d8dd; border-radius: var(--card-radius); padding: 16px; text-align: center; background: #fafbfc; }
    .ad-label { font-size: 11px; color: #a2a8b1; text-transform: uppercase; letter-spacing: 1.2px; }
    .ad-placeholder { padding: 40px 10px; color: #c2c6cc; }
    .ad-placeholder i { font-size: 32px; }
    .ad-placeholder p { font-size: 13px; margin-top: 8px; }

    /* ===== LIST layout (राजनीति) ===== */
    .list-layout { display: flex; flex-direction: column; gap: 12px; }
    .list-row { display: flex; align-items: center; gap: 16px; background: #fff; border-radius: var(--card-radius); padding: 14px; box-shadow: var(--shadow-sm); transition: var(--transition); }
    .list-row:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .list-row-num { font-size: 24px; font-weight: 800; min-width: 30px; opacity: .3; }
    .list-row-img { flex-shrink: 0; }
    .list-row img { width: 170px; height: 125px; object-fit: cover; border-radius: 8px; }
    .list-row-body h5 { font-size: 19px; font-weight: 700; margin-bottom: 4px; line-height: 1.4; }
    .list-row-body h5 a { color: var(--text-dark); transition: var(--transition); }
    .list-row-body h5 a:hover { color: #e30613; }
    .list-row-body p { font-size: 15px; color: var(--text-muted); margin-bottom: 6px; line-height: 1.5; }

    /* ===== STAT layout (अर्थतन्त्र) ===== */
    .stat-layout { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
    .stat-card { background: #fff; border-radius: var(--card-radius); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition); }
    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .stat-card-img { position: relative; overflow: hidden; }
    .stat-card-img img { width: 100%; height: 210px; object-fit: cover; transition: transform .35s; }
    .stat-card:hover .stat-card-img img { transform: scale(1.05); }
    .stat-badge { position: absolute; top: 10px; right: 10px; color: #fff; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px; backdrop-filter: blur(4px); }
    .stat-card-body { padding: 16px; }
    .stat-card-body h5 { font-size: 18px; font-weight: 700; margin-bottom: 8px; line-height: 1.4; }
    .stat-card-body h5 a { color: var(--text-dark); transition: var(--transition); }
    .stat-card-body h5 a:hover { color: #e30613; }
    .stat-card-body p { font-size: 15px; color: var(--text-muted); line-height: 1.5; }

    /* ===== BIG-GRID layout (खेलकुद) ===== */
    .biggrid-hero { position: relative; border-radius: 12px; overflow: hidden; height: 420px; margin-bottom: 20px; box-shadow: var(--shadow-md); }
    .biggrid-hero img { width: 100%; height: 100%; object-fit: cover; transition: transform .45s; }
    .biggrid-hero:hover img { transform: scale(1.04); }
    .biggrid-hero-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.9)); padding: 30px 28px 24px; }
    .biggrid-hero-tag { display: inline-block; color: #fff; font-size: 11px; font-weight: 700; padding: 5px 13px; border-radius: 20px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .biggrid-hero-overlay h3 { color: #fff; font-size: 29px; font-weight: 800; margin-bottom: 8px; line-height: 1.35; }
    .biggrid-hero-overlay p { color: #e4e4e4; font-size: 16px; margin-bottom: 8px; max-width: 640px; line-height: 1.5; }

    .biggrid-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .biggrid-card { position: relative; border-radius: 10px; overflow: hidden; height: 230px; box-shadow: var(--shadow-sm); }
    .biggrid-card img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
    .biggrid-card:hover img { transform: scale(1.06); }
    .biggrid-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.88)); padding: 14px; }
    .biggrid-overlay h5 { color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 4px; line-height: 1.35; }

    /* ===== MASONRY layout (मनोरञ्जन) ===== */
    .masonry-layout { column-count: 2; column-gap: 16px; }
    .masonry-card { break-inside: avoid; margin-bottom: 16px; position: relative; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition); }
    .masonry-card:hover { box-shadow: var(--shadow-md); }
    .masonry-card img { width: 100%; display: block; height: 230px; object-fit: cover; transition: transform .35s; }
    .masonry-card:hover img { transform: scale(1.05); }
    .masonry-card.tall img { height: 360px; }
    .masonry-caption { color: #fff; font-size: 16px; font-weight: 700; padding: 14px 14px 12px; line-height: 1.4; }

    /* ===== MINIMAL layout (प्रविधि) — image-based ===== */
    .minimal-layout { display: flex; flex-direction: column; gap: 4px; }
    .minimal-card { display: flex; gap: 18px; align-items: flex-start; padding: 18px 0; border-bottom: 1px solid var(--border-soft); transition: var(--transition); }
    .minimal-card:last-child { border-bottom: none; }
    .minimal-card:hover { background: #fafbfc; margin: 0 -12px; padding: 18px 12px; border-radius: 8px; }
    .minimal-img { position: relative; flex-shrink: 0; width: 170px; height: 128px; border-radius: 8px; overflow: hidden; }
    .minimal-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
    .minimal-card:hover .minimal-img img { transform: scale(1.06); }
    .minimal-tag { position: absolute; bottom: 6px; left: 6px; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.25); }
    .minimal-body h5 { font-size: 19px; font-weight: 700; margin-bottom: 6px; line-height: 1.4; }
    .minimal-body h5 a { color: var(--text-dark); transition: var(--transition); }
    .minimal-body h5 a:hover { color: #e30613; }
    .minimal-body p { font-size: 15px; color: var(--text-muted); line-height: 1.55; }

    /* ===== TIMELINE layout (विश्व) ===== */
    .timeline-layout { position: relative; padding-left: 32px; border-left: 2px solid var(--border-soft); }
    .timeline-item { position: relative; margin-bottom: 30px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot { position: absolute; left: -39px; top: 5px; width: 14px; height: 14px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px var(--border-soft); }
    .timeline-date { font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .timeline-content h5 { font-size: 19px; font-weight: 700; margin: 6px 0 8px; line-height: 1.4; }
    .timeline-content h5 a { color: var(--text-dark); transition: var(--transition); }
    .timeline-content h5 a:hover { color: #e30613; }
    .timeline-content p { font-size: 15px; color: var(--text-muted); line-height: 1.55; }

    /* ===== ICON-CARD layout (स्वास्थ्य) ===== */
    .iconcard-layout { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
    .iconcard { background: #fff; border-radius: 12px; overflow: hidden; position: relative; box-shadow: var(--shadow-sm); transition: var(--transition); }
    .iconcard:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .iconcard-top { position: absolute; top: 12px; left: 12px; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; z-index: 2; box-shadow: 0 3px 8px rgba(0,0,0,0.2); }
    .iconcard-img { overflow: hidden; }
    .iconcard-img img { width: 100%; height: 200px; object-fit: cover; transition: transform .35s; }
    .iconcard:hover .iconcard-img img { transform: scale(1.06); }
    .iconcard-body { padding: 16px; }
    .iconcard-body h5 { font-size: 18px; font-weight: 700; margin-bottom: 8px; line-height: 1.4; }
    .iconcard-body h5 a { color: var(--text-dark); transition: var(--transition); }
    .iconcard-body h5 a:hover { color: #e30613; }
    .iconcard-body p { font-size: 15px; color: var(--text-muted); line-height: 1.5; }

    @media (max-width: 768px) {
        .stat-layout, .biggrid-grid, .iconcard-layout { grid-template-columns: 1fr; }
        .masonry-layout { column-count: 1; }
        .list-row { flex-direction: column; align-items: flex-start; }
        .list-row img { width: 100%; height: 200px; }
        .minimal-img { width: 100%; height: 190px; }
        .minimal-card { flex-direction: column; }
        .biggrid-hero { height: 280px; }
        .biggrid-hero-overlay h3 { font-size: 19px; }
        .cat-banner { padding: 22px; }
        .cat-banner h2 { font-size: 21px; }
    }
</style>
@endpush