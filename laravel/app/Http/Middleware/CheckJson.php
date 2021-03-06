<?php

namespace App\Http\Middleware;

use Closure;

class CheckJson
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
        if (count($request->all()) > 0 && !$request->isJson()) {
            abort(400, 'Json required.');
        }

        return $next($request);
    }
}
