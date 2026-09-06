@extends('frontend.layouts.app')

@section('title', $news->title)

@section('content')

<nav class="breadcrumb-nav">
    <a href="{{ route('home') }}">गृहपृष्ठ</a>
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
                <i class="bi bi-eye"></i> {{ $news->views }} पटक हेरिएको
            </div>
        </div>

        <img src="{{ $news->image ? asset('storage/'.$news->image) : 'https://placehold.co/900x450?text=No+Image' }}" class="article-hero-img">

        <div class="article-body">
            {!! nl2br(e($news->body)) !!}
        </div>

        <div class="article-tags">
            <span class="tag-label">ट्याग:</span>
            <a href="{{ route('category.show', $news->category->slug) }}" class="tag">{{ $news->category->name }}</a>
            <a href="#" class="tag">समाचार</a>
        </div>

        <div class="share-bar">
            <strong>यो समाचार सेयर गर्नुहोस्:</strong>
            <div class="share-icons">
                <a href="#" class="share-btn fb"><i class="bi bi-facebook"></i></a>
                <a href="#" class="share-btn tw"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="share-btn wa"><i class="bi bi-whatsapp"></i></a>
                <a href="#" class="share-btn tg"><i class="bi bi-telegram"></i></a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="sidebar-box mb-4">
            <h5 class="sidebar-title">सम्बन्धित समाचार</h5>
            @forelse($related as $item)
                <div class="related-item">
                    <a href="{{ route('news.show', $item->slug) }}">
                        <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/100x80?text=No+Image' }}" class="related-img">
                    </a>
                    <div>
                        <a href="{{ route('news.show', $item->slug) }}" class="related-title">{{ $item->title }}</a>
                        <div class="news-meta">{{ $item->published_at?->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p class="news-meta">यस श्रेणीमा अन्य समाचार छैन।</p>
            @endforelse
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

    .sidebar-box { background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 18px; }
    .sidebar-title { font-weight: 800; margin-bottom: 16px; border-bottom: 2px solid #e30613; padding-bottom: 8px; display: inline-block; }
    .related-item { display: flex; gap: 12px; margin-bottom: 16px; }
    .related-img { width: 80px; height: 60px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
    .related-title { font-size: 14px; font-weight: 600; color: #222; line-height: 1.4; display: block; }
    .related-title:hover { color: #e30613; }

    .sidebar-ad-box { border: 1px dashed #ccc; border-radius: 6px; padding: 16px; text-align: center; }
    .ad-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 1px; }
    .ad-placeholder { padding: 40px 10px; color: #bbb; }
    .ad-placeholder i { font-size: 32px; }
    .ad-placeholder p { font-size: 13px; margin-top: 8px; }
</style>
@endpush