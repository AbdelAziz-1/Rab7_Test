<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   
        public function handle(Request $request, Closure $next)
        {
            $locale = $request->header('Accept-Language', 'en'); // الافتراضي إنجليزي
            if (in_array($locale, ['en', 'ar'])) {
                App::setLocale($locale);
            }
            return $next($request);
        }
    }