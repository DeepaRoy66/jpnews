<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('frontend.layouts.app', function ($view) {
            $locale = app()->getLocale();

            $categories = Category::with('translations')->get()->filter(function ($cat) use ($locale) {
                return $cat->nameIn($locale) !== null;
            })->values();

            $view->with([
                'navbarAd'      => Ad::active('navbar')->latest()->first(),
                'navCategories' => $categories,
                'breakingNews'  => News::published()->where('locale', $locale)->latest('published_at')->take(8)->get(),
            ]);
        });
    }
}