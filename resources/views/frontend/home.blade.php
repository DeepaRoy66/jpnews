@extends('frontend.layouts.app')

@section('title', $categoryName ?? 'गृहपृष्ठ')

@section('content')

@php $first = $news->first(); @endphp

@if($first)
    <div class="hero-grid mb-4">
        <div class="hero-main">
            <a href="{{ route('news.show', $first->slug) }}">
                <img src="{{ $first->image ? asset('storage/'.$first->image) : 'https://placehold.co/900x550?text=No+Image' }}" class="hero-main-img">
            </a>
            <div class="hero-main-overlay">
                <span class="badge-category">{{ $first->category->name }}</span>
                <h2 class="hero-title"><a href="{{ route('news.show', $first->slug) }}">{{ $first->title }}</a></h2>
                <div class="news-meta light">
                    <i class="bi bi-person"></i> {{ $first->author->name ?? 'MeroNews' }}
                    &nbsp;.&nbsp;
                    <i class="bi bi-clock"></i> {{ $first->published_at?->diffForHumans() }}
                </div>
            </div>
        </div>

        <div class="hero-side">
            @foreach($news->skip(1)->take(3) as $side)
                <div class="hero-side-item">
                    <a href="{{ route('news.show', $side->slug) }}">
                        <img src="{{ $side->image ? asset('storage/'.$side->image) : 'https://placehold.co/300x200?text=No+Image' }}">
                    </a>
                    <div>
                        <span class="badge-category small">{{ $side->category->name }}</span>
                        <h6><a href="{{ route('news.show', $side->slug) }}">{{ Str::limit($side->title, 60) }}</a></h6>
                        <div class="news-meta">{{ $side->published_at?->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <h4 class="section-title">{{ $categoryName ?? 'ताजा समाचार' }}</h4>

        <div class="news-list">
            @php $listNews = $news->skip(4); @endphp
            @forelse($listNews as $item)
                <div class="news-row">
                    <a href="{{ route('news.show', $item->slug) }}" class="news-row-img">
                        <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/220x150?text=No+Image' }}">
                    </a>
                    <div class="news-row-body">
                        <span class="badge-category small">{{ $item->category->name }}</span>
                        <h5><a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a></h5>
                        <p>{{ Str::limit($item->excerpt, 110) }}</p>
                        <div class="news-meta">
                            <i class="bi bi-person"></i> {{ $item->author->name ?? 'MeroNews' }}
                            &nbsp;.&nbsp;
                            <i class="bi bi-clock"></i> {{ $item->published_at?->diffForHumans() }}
                            &nbsp;.&nbsp;
                            <i class="bi bi-eye"></i> {{ $item->views }}
                        </div>
                    </div>
                </div>
            @empty
                @if($news->count() <= 4)
                    <p>यस श्रेणीमा हाल थप कुनै समाचार छैन।</p>
                @endif
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $news->links() }}
        </div>
    </div>

    <div class="col-lg-4">
        <div class="sidebar-box mb-4">
            <h5 class="sidebar-title"><i class="bi bi-fire text-danger"></i> बढी पढिएका</h5>
            <ul class="trending-list">
                @forelse($trending as $index => $t)
                    <li>
                        <span class="trending-number">{{ $index + 1 }}</span>
                        <div>
                            <a href="{{ route('news.show', $t->slug) }}" class="trending-title">{{ $t->title }}</a>
                            <div class="news-meta"><i class="bi bi-eye"></i> {{ $t->views }} पटक</div>
                        </div>
                    </li>
                @empty
                    <li>अहिलेसम्म डाटा छैन।</li>
                @endforelse
            </ul>
        </div>

        <div class="sidebar-ad-box">
            <span class="ad-label">विज्ञापन</span>
            <div class="ad-placeholder">
                <i class="bi bi-image"></i>
                <p>Advertisement Space</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .badge-category { display: inline-block; background: #e30613; color: #fff; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 3px; margin-bottom: 8px; }
    .badge-category.small { font-size: 11px; padding: 2px 8px; }
    .news-meta { font-size: 13px; color: #888; margin-top: 4px; }
    .news-meta.light { color: #eee; }
    .section-title { font-weight: 800; border-bottom: 3px solid #e30613; display: inline-block; padding-bottom: 6px; margin-bottom: 20px; }

    /* Hero grid */
    .hero-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 16px; }
    .hero-main { position: relative; border-radius: 8px; overflow: hidden; height: 460px; }
    .hero-main-img { width: 100%; height: 100%; object-fit: cover; }
    .hero-main-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.85));
        padding: 30px 24px 20px;
        color: #fff;
    }
    .hero-title { font-size: 26px; font-weight: 800; line-height: 1.3; margin: 6px 0 8px; }
    .hero-title a { color: #fff; }
    .hero-title a:hover { color: #ffd1d1; }

    .hero-side { display: flex; flex-direction: column; gap: 14px; }
    .hero-side-item { display: flex; gap: 10px; background: #fff; border: 1px solid #eee; border-radius: 6px; overflow: hidden; padding: 8px; }
    .hero-side-item img { width: 90px; height: 70px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
    .hero-side-item h6 { font-size: 14px; font-weight: 700; margin: 4px 0 4px; line-height: 1.3; }
    .hero-side-item h6 a { color: #111; }
    .hero-side-item h6 a:hover { color: #e30613; }

    /* News list rows */
    .news-list { display: flex; flex-direction: column; gap: 18px; }
    .news-row { display: flex; gap: 16px; border-bottom: 1px solid #eee; padding-bottom: 18px; }
    .news-row-img img { width: 220px; height: 150px; object-fit: cover; border-radius: 6px; }
    .news-row-body h5 { font-size: 18px; font-weight: 700; margin: 4px 0 8px; }
    .news-row-body h5 a { color: #111; }
    .news-row-body h5 a:hover { color: #e30613; }
    .news-row-body p { font-size: 14px; color: #666; margin-bottom: 8px; }

    /* Sidebar */
    .sidebar-box { background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 18px; }
    .sidebar-title { font-weight: 800; margin-bottom: 16px; }
    .trending-list { list-style: none; padding: 0; margin: 0; }
    .trending-list li { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid #eee; }
    .trending-list li:last-child { border-bottom: none; }
    .trending-number { font-weight: 800; font-size: 20px; color: #e30613; min-width: 24px; }
    .trending-title { font-size: 14px; font-weight: 600; color: #222; line-height: 1.4; }
    .trending-title:hover { color: #e30613; }

    .sidebar-ad-box { border: 1px dashed #ccc; border-radius: 6px; padding: 16px; text-align: center; }
    .ad-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 1px; }
    .ad-placeholder { padding: 40px 10px; color: #bbb; }
    .ad-placeholder i { font-size: 32px; }
    .ad-placeholder p { font-size: 13px; margin-top: 8px; }

    @media (max-width: 768px) {
        .hero-grid { grid-template-columns: 1fr; }
        .hero-main { height: 300px; }
        .news-row { flex-direction: column; }
        .news-row-img img { width: 100%; height: 200px; }
    }
</style>
@endpush