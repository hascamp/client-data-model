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
            $main->requestion('setHeaderToResource');
        });

        if (! $request->direction()->visitPermission()) {
            abort(403);
        }

        // dd($request->direction()->requestion('ping', 'call.ping:index'));
        dd(\Hascamp\Client\Resource::data('call.ping:index'));
        return $next($request);
    }
}
