<?php

namespace Hascamp\Direction\Middleware;

use Closure;
use Illuminate\Http\Request;
use Hascamp\Direction\Exceptions\AppIdentifier;

class TrackingVisit
{
    public function handle(Request $request, Closure $next)
    {
        $request->trackingVisit($request, function ($main) {
            $main->visitBuilder();
        });

        if (! $request->direction()->visitPermission()) {
            dd($request->direction());
            report(new AppIdentifier("Unable to identify client application."));
            abort(403);
        }

        // session()->forget('_BASE_META_IDENTIFIED');
        // dd(session()->all());
        // dd($request->direction()->request('call.ping:index'));
        return $next($request);
    }
}
