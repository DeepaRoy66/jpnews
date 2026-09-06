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
            $view->with([
                'navbarAd'     => Ad::active('navbar')->latest()->first(),
                'navCategories' => Category::orderBy('name')->get(),
                'breakingNews' => News::published()->latest('published_at')->take(8)->get(),
            ]);
        });
    }
}