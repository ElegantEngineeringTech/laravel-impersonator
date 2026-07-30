<?php

declare(strict_types=1);

namespace Elegantly\Impersonator\Http\Middleware;

use Closure;
use Elegantly\Impersonator\Facades\Impersonator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeaveImpersonatorMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Impersonator::leave();

        return $next($request);
    }
}
