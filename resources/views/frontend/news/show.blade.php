@extends('frontend.layouts.app')

@section('title', $news->title)

@section('content')

<nav class="breadcrumb-nav">
    <a href="{{ route('home') }}">{{ __('site.home') }}</a>
    <i class="bi bi-chevron-right"></i>
    <a href="{{ route('category.show', $news->category->slug) }}">{{ $news->category->name }}</a>
</nav>

<div class="row">
    <div class="col-lg-8">
        <span class="badge-category">{{ $news->category->name }}</span>
        <h1 class="article-title">{{ $news->title }}</h1>
        <p class="article-excerpt">{{ $news->excerpt }}</p>

        <div class="article-meta-bar">
            <div class="author-info">
                <div class="author-avatar">{{ mb_substr($news->author->name ?? 'M', 0, 1) }}</div>
                <div>
                    <div class="author-name">{{ $news->author->name ?? 'MeroNews' }}</div>
                    <div class="news-meta">{{ $news->published_at?->format('F j, Y') }} • {{ $news->published_at?->diffForHumans() }}</div>
                </div>
            </div>
            <div class="article-views">
                <i class="bi bi-eye"></i> {{ $news->views }} {{ __('site.times_viewed') }}
            </div>
        </div>

        <img src="{{ $news->image ? asset('storage/'.$news->image) : 'https://placehold.co/900x450?text=No+Image' }}" class="article-hero-img">

        <div class="article-body">
            {!! nl2br(e($news->body)) !!}
        </div>

        <div class="article-tags">
            <span class="tag-label">{{ __('site.tag_label') }}</span>
            <a href="{{ route('category.show', $news->category->slug) }}" class="tag">{{ $news->category->name }}</a>
            <a href="#" class="tag">{{ __('site.news_tag') }}</a>
        </div>

        <div class="share-bar">
            <strong>{{ __('site.share_this') }}</strong>
            <div class="share-icons">
                <a href="#" class="share-btn fb"><i class="bi bi-facebook"></i></a>
                <a href="#" class="share-btn tw"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="share-btn wa"><i class="bi bi-whatsapp"></i></a>
                <a href="#" class="share-btn tg"><i class="bi bi-telegram"></i></a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- ================= RELATED NEWS ================= --}}
        <div class="rel-box mb-4">
            <h5 class="rel-title">{{ __('site.related_news') }}</h5>

            @forelse($related as $item)
                <a href="{{ route('news.show', $item->slug) }}" class="rel-item">
                    <span class="rel-thumb">
                        <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/200x175?text=No+Image' }}"
                             alt="{{ $item->title }}" loading="lazy">
                    </span>
                    <span class="rel-text">{{ Str::limit($item->title, 85) }}</span>
                </a>
            @empty
                <p class="news-meta">{{ __('site.no_related_news') }}</p>
            @endforelse
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
    .breadcrumb-nav { font-size: 13px; color: #888; margin-bottom: 16px; }
    .breadcrumb-nav a { color: #888; }
    .breadcrumb-nav a:hover { color: #e30613; }
    .breadcrumb-nav i { font-size: 10px; margin: 0 6px; }

    .badge-category { display: inline-block; background: #e30613; color: #fff; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 3px; margin-bottom: 12px; }
    .article-title { font-size: 32px; font-weight: 800; line-height: 1.3; margin-bottom: 12px; }
    .article-excerpt { font-size: 17px; color: #555; margin-bottom: 20px; line-height: 1.6; }

    .article-meta-bar {
        display: flex; justify-content: space-between; align-items: center;
        border-top: 1px solid #eee; border-bottom: 1px solid #eee;
        padding: 14px 0; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;
    }
    .author-info { display: flex; align-items: center; gap: 12px; }
    .author-avatar {
        width: 42px; height: 42px; border-radius: 50%; background: #e30613; color: #fff;
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;
    }
    .author-name { font-weight: 700; font-size: 14px; }
    .news-meta { font-size: 13px; color: #888; }
    .article-views { font-size: 13px; color: #888; }

    .article-hero-img { width: 100%; max-height: 480px; object-fit: cover; border-radius: 8px; margin-bottom: 24px; }
    .article-body { font-size: 17px; line-height: 1.9; color: #222; margin-bottom: 24px; }

    .article-tags { margin-bottom: 24px; }
    .tag-label { font-weight: 700; margin-right: 8px; }
    .tag { display: inline-block; background: #f1f1f1; color: #333; font-size: 13px; padding: 5px 14px; border-radius: 20px; margin-right: 6px; }
    .tag:hover { background: #e30613; color: #fff; }

    .share-bar { display: flex; align-items: center; gap: 14px; padding-top: 18px; border-top: 1px solid #eee; flex-wrap: wrap; }
    .share-icons { display: flex; gap: 8px; }
    .share-btn { width: 38px; height: 38px; border-radius: 50%; background: #f1f1f1; display: inline-flex; align-items: center; justify-content: center; color: #333; font-size: 16px; }
    .share-btn.fb:hover { background: #1877f2; color: #fff; }
    .share-btn.tw:hover { background: #000; color: #fff; }
    .share-btn.wa:hover { background: #25d366; color: #fff; }
    .share-btn.tg:hover { background: #0088cc; color: #fff; }

    /* ===== related news: sano image bayaan, thulo bold title dahine, bich ma patalo line ===== */
    .rel-box { background: transparent; border: none; box-shadow: none; padding: 0; }
    .rel-title {
        font-weight: 800; font-size: 20px; margin: 0 0 6px; padding-bottom: 10px;
        border-bottom: 3px solid #e30613; display: inline-block;
    }
    .rel-item {
        display: flex; align-items: center; gap: 18px;
        padding: 22px 0; border-bottom: 1px solid #e6e1d8;
    }
    .rel-item:last-child { border-bottom: none; }
    .rel-thumb {
        flex-shrink: 0; width: 100px; aspect-ratio: 8 / 7;
        border-radius: 8px; overflow: hidden; background: #f6f4f1;
    }
    .rel-thumb img {
        display: block; width: 100%; height: 100%; object-fit: cover;
        transition: transform .3s ease;
    }
    .rel-item:hover .rel-thumb img { transform: scale(1.08); }
    .rel-text { font-size: 19px; font-weight: 800; line-height: 1.45; color: #16181c; }
    .rel-item:hover .rel-text { color: #e30613; }

    .sidebar-ad-box { border: 1px dashed #ccc; border-radius: 6px; padding: 16px; text-align: center; }
    .ad-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 1px; }
    .ad-placeholder { padding: 40px 10px; color: #bbb; }
    .ad-placeholder i { font-size: 32px; }
    .ad-placeholder p { font-size: 13px; margin-top: 8px; }
</style>
@endpush