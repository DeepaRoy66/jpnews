<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App;

class ForceLocale
{
    /**
     * Usage in routes: ->middleware('locale:en')  or  ->middleware('locale:ne')
     * The locale is now determined by WHICH DOMAIN the request came in on,
     * not by session — so links, sharing, and SEO all stay consistent
     * per-domain (exactly like english.onlinekhabar.com vs onlinekhabar.com).
     */
    public function handle(Request $request, Closure $next, string $locale)
    {
        App::setLocale($locale);

        return $next($request);
    }
}