<?php

namespace Hascamp\Direction\Middleware;

use Closure;
use Illuminate\Http\Request;

class VisitorsAsClient
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
