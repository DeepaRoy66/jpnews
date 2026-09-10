<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;

class HomeController extends Controller
{
    private array $categoryStyles = [
        'rajniti'      => ['layout' => 'list',      'color' => '#1f3a5f', 'icon' => 'bi-bank'],
        'arthatantra'  => ['layout' => 'stat',       'color' => '#1a7a4c', 'icon' => 'bi-graph-up-arrow'],
        'khelkud'      => ['layout' => 'big-grid',   'color' => '#d95d1e', 'icon' => 'bi-trophy'],
        'manoranjan'   => ['layout' => 'masonry',    'color' => '#8e2d8e', 'icon' => 'bi-film'],
        'prabidhi'     => ['layout' => 'minimal',    'color' => '#0d7c86', 'icon' => 'bi-cpu'],
        'bishwa'       => ['layout' => 'timeline',   'color' => '#2b2b2b', 'icon' => 'bi-globe-asia-australia'],
        'swasthya'     => ['layout' => 'icon-card',  'color' => '#2e9e5b', 'icon' => 'bi-heart-pulse'],
    ];

    public function index()
    {
        $locale = app()->getLocale();

        $news = News::with(['category', 'author'])
            ->published()
            ->where('locale', $locale)
            ->latest('published_at')
            ->paginate(9);

        $trending = News::with('category')
            ->published()
            ->where('locale', $locale)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return view('frontend.home', [
            'news' => $news,
            'trending' => $trending,
            'categoryName' => null,
        ]);
    }

    public function category($slug)
    {
        $locale = app()->getLocale();
        $category = Category::where('slug', $slug)->firstOrFail();

        $news = $category->news()
            ->with(['category', 'author'])
            ->published()
            ->where('locale', $locale)
            ->latest('published_at')
            ->paginate(9);

        $trending = News::with('category')
            ->published()
            ->where('locale', $locale)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        $style = $this->categoryStyles[$slug] ?? ['layout' => 'list', 'color' => '#e30613', 'icon' => 'bi-newspaper'];

        return view('frontend.category', [
            'news' => $news,
            'trending' => $trending,
            'categoryName' => $category->nameIn($locale) ?? $category->slug,
            'categorySlug' => $slug,
            'layout' => $style['layout'],
            'accent' => $style['color'],
            'icon' => $style['icon'],
        ]);
    }
}