<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function show($slug)
    {
        $news = News::with(['category', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $news->increment('views');

        $related = News::with('category')
            ->published()
            ->where('category_id', $news->category_id)
            ->where('locale', $news->locale)
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('frontend.news.show', compact('news', 'related'));
    }
}