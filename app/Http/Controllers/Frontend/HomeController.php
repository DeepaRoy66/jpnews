<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;

class HomeController extends Controller
{
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

        return view('frontend.home', [
            'news' => $news,
            'trending' => $trending,
            'categoryName' => $category->nameIn($locale) ?? $category->slug,
        ]);
    }
}