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

        $categories = Category::with('translations')->get()->filter(function ($cat) use ($locale) {
            return $cat->nameIn($locale) !== null;
        });

        $categorySections = [];
        foreach ($categories as $cat) {
            $catNews = News::with(['category', 'author'])
                ->published()
                ->where('locale', $locale)
                ->where('category_id', $cat->id)
                ->latest('published_at')
                ->take(4)
                ->get();

            if ($catNews->isEmpty()) {
                continue;
            }

            $categorySections[] = [
                'category' => $cat,
                'name' => $cat->nameIn($locale),
                'slug' => $cat->slug,
                'accent' => $cat->accent_color,
                'icon' => $cat->icon,
                'items' => $catNews,
            ];
        }

        return view('frontend.home', [
            'news' => $news,
            'trending' => $trending,
            'categoryName' => null,
            'categorySections' => $categorySections,
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

        return view('frontend.category', [
            'news' => $news,
            'trending' => $trending,
            'categoryName' => $category->nameIn($locale) ?? $category->slug,
            'categorySlug' => $slug,
            'layout' => $category->layout_type,
            'accent' => $category->accent_color,
            'icon' => $category->icon,
        ]);
    }
}