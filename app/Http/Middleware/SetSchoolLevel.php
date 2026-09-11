<?php

namespace App\Http\Middleware;

use App\Support\SchoolLevel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SetSchoolLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->query->has('school_level')) {
            $input = $request->validate([
                'school_level' => ['required', 'string', Rule::in(['all', ...array_keys(SchoolLevel::OPTIONS)])],
            ]);
            $request->session()->put('school_level', $input['school_level']);
        }

        return $next($request);
    }
}
