<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ForceLocale
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        $locale = str_starts_with($host, 'english.') ? 'en' : 'ne';

        App::setLocale($locale);

        return $next($request);
    }
}