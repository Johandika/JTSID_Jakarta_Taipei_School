<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale(match ($request->segment(1)) {
            'zh' => 'zh-TW', 'id' => 'id', default => 'en'
        });

        return $next($request);
    }
}
