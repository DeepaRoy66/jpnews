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

        // navbar मा वास्तवमा (checkbox + take(6) पछि) देखिने categories को ID निकाल्ने —
        // यहि exact query हो जुन ViewServiceProvider ले navbar बनाउँदा प्रयोग गर्छ
        $navbarCategoryIds = Category::inNavbarFor($locale)->take(6)->pluck('id');

        // homepage मा ती ID बाहेक बाँकी सबै categories
        try {
            $hiddenCategories = Category::with(['translations', 'subcategories'])
                ->whereNotIn('id', $navbarCategoryIds)
                ->get()
                ->filter(fn ($cat) => $cat->nameIn($locale) !== null);
            $hasSubcatRelation = true;
        } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
            $hiddenCategories = Category::with(['translations'])
                ->whereNotIn('id', $navbarCategoryIds)
                ->get()
                ->filter(fn ($cat) => $cat->nameIn($locale) !== null);
            $hasSubcatRelation = false;
        }

        $categorySections = [];
        foreach ($hiddenCategories as $cat) {
            $catNews = News::with(['category', 'author'])
                ->published()
                ->where('locale', $locale)
                ->where('category_id', $cat->id)
                ->latest('published_at')
                ->take(8)
                ->get();

            if ($catNews->isEmpty()) {
                continue;
            }

            $subcategories = $hasSubcatRelation ? $cat->subcategories : collect();

            $subWidget = null;
            if ($firstSub = $subcategories->first()) {
                $subItems = News::with('category')
                    ->published()
                    ->where('locale', $locale)
                    ->where('category_id', $firstSub->id)
                    ->latest('published_at')
                    ->take(6)
                    ->get();

                if ($subItems->isNotEmpty()) {
                    $subWidget = [
                        'title'        => $firstSub->nameIn($locale) ?? $firstSub->name,
                        'view_all_url' => route('category.show', $firstSub->slug),
                        'items'        => $subItems,
                    ];
                }
            }

            $categorySections[] = [
                'title'         => $cat->nameIn($locale),
                'accent_color'  => $cat->accent_color,
                'icon'          => $cat->icon,
                'layout'        => $cat->layout_type,
                'view_all_url'  => route('category.show', $cat->slug),
                'items'         => $catNews,
                'subcats'       => $subcategories
                    ->map(fn ($sub) => [
                        'title' => $sub->nameIn($locale) ?? $sub->name,
                        'url'   => route('category.show', $sub->slug),
                    ])
                    ->values()
                    ->all(),
                'subwidget'     => $subWidget,
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