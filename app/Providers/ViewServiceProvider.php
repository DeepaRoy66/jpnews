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

            $navCategories = Category::inNavbarFor($locale)
                ->with('translations')
                ->take(6)
                ->get();

            $view->with([
                'navbarAd'      => Ad::active('navbar')->latest()->first(),
                'footerAds'     => Ad::active('footer')->latest()->take(5)->get(),
                'navCategories' => $navCategories,
                'breakingNews'  => News::published()->where('locale', $locale)->latest('published_at')->take(8)->get(),
            ]);
        });
    }
}