<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App;

class ForceLocale
{
    /**
     * No route parameter needed anymore. We look at the actual domain
     * the request came in on: english.<domain> => 'en', anything else => 'ne'.
     * This means route('news.show', ...) etc. only ever needs to be
     * defined ONCE — Laravel will naturally keep links on whichever
     * domain the visitor is currently browsing.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->getHost() === 'english.' . config('app.domain')
            ? 'en'
            : 'ne';

        App::setLocale($locale);

        return $next($request);
    }
}