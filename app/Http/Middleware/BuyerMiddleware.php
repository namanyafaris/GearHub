<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BuyerMiddleware
{
    /**
     * Ensure the authenticated user is a buyer.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isBuyer()) {
            abort(403, 'Akses khusus buyer.');
        }

        return $next($request);
    }
}
