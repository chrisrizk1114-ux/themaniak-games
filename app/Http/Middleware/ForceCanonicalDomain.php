<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('production')) {
            return $next($request);
        }

        $host = strtolower($request->getHost());

        if (str_starts_with($host, 'www.')) {
            $canonical = substr($host, 4);
            $target = 'https://'.$canonical.$request->getRequestUri();

            return redirect()->away($target, 301);
        }

        return $next($request);
    }
}
