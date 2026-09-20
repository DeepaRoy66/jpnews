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

        // homepage मा ती ID बाहेक बाँकी सबै categories (checkbox tick भए पनि
        // navbar मा ठाउँ नपाएका हुन सक्छन् — तिनलाई पनि यहाँ समावेश गर्ने)
        //
        // ⚠️ 'subcategories' relation लाई तेरो Category model मा जोड़ — यदि
        // categories self-referencing (parent_id) छन् भने:
        //   public function subcategories() { return $this->hasMany(Category::class, 'parent_id'); }
        // Relation add नगरेसम्म पनि crash नहोस् भनेर try/catch राखेको छु — तल हेर।
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
                ->take(8) // 'grid' ले 6 wota, 'text-list' ले 8 wota, 'lead-list' ले lead+rest लिन्छ — 8 सुरक्षित संख्या
                ->get();

            if ($catNews->isEmpty()) {
                continue;
            }

            // subcategories relation नभए यी दुबै खाली/null नै रहन्छन् — कुनै crash हुँदैन
            $subcategories = $hasSubcatRelation ? $cat->subcategories : collect();

            // ⚠️ optional: एउटा sub-category लाई "sub-widget" (कर्पोरेट-जस्तो) को रूपमा
            // छुट्टै छोटो card widget बनाउन चाहेमा — यहाँ पहिलो subcategory बाट 6 wota
            // news तानेर देखाउने। नचाहिए यो block नै हटाइदिनु।
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
                // ⚠️ यहि field ले homepage मा layout decide गर्छ — index%2 होइन।
                // Category admin form मा एउटा select add गर: block-list / lead-list / text-list / grid
                'layout'        => $cat->layout_type,
                'view_all_url'  => route('category.show', $cat->slug),
                'items'         => $catNews,
                // onlinekhabar-style chip nav (बिजनेस मुनि: अर्थनीति, पर्यटन...) —
                // subcategories() relation नभए यो खाली array नै रहन्छ, blade ले skip गर्छ
                'subcats'       => $subcategories
                    ->map(fn ($sub) => [
                        'title' => $sub->nameIn($locale) ?? $sub->name,
                        'url'   => route('category.show', $sub->slug),
                    ])
                    ->values()
                    ->all(),
                // कर्पोरेट-जस्तो सानो secondary widget (optional — null भए blade ले skip गर्छ)
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