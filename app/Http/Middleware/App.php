<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class App
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Session::has('locale'))
        {
            Session::put('locale', 'sl');
        }
        if ($request->input('lang')) {
            $lang = $request->input('lang');
            $locale = $lang == 'en' ? 'en' : 'sl';
            Session::put('locale', $locale);
        }

        app()->setLocale(Session::get('locale'));

        return $next($request);
    }
}
