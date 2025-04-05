<?php

namespace Hascamp\Direction\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrackingVisit
{
    public function handle(Request $request, Closure $next)
    {
        $request->trackingVisit($request, function ($main) {
            $main->visitBuilder();
        });

        if (! $request->direction()->visitPermission()) {
            abort(403);
        }

        // session()->forget('_BASE_META_IDENTIFIED');
        // dd(session()->all());
        dd($request->direction()->request('call.ping:index'));
        return $next($request);
    }
}
