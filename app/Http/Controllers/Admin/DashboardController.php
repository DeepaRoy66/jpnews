<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\News;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'total_news'      => News::count(),
            'published_news'  => News::where('is_published', true)->count(),
            'total_categories'=> Category::count(),
            'active_ads'      => Ad::where('is_active', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
