<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJson
{
    /**
     * Add x-requested-with: XMLHttpRequest to the request header
     * to force all responses automatically converted to json
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
