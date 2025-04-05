<?php

namespace Hascamp\Direction;

use Closure;
use Illuminate\Http\Request;
use Hascamp\Direction\Builder\Main;
use Hascamp\Direction\Builder\Services\Request as ServicesRequest;
use Illuminate\Support\ServiceProvider;
use Hascamp\Direction\Contracts\Accessible;
use Hascamp\Direction\Middleware\TrackingVisit;
use Illuminate\Contracts\Foundation\Application;
use Hascamp\Direction\Middleware\VisitorsAsClient;

class DirectionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->register_config();

        $this->app->scoped(Accessible::class, function (Application $app) {
            return new Main(new ServicesRequest, $app->config['direction']);
        });
    }
    
    public function boot(): void
    {
        $this->boot_macro();
        $this->boot_middleware();
    }

    private function register_config(): void
    {
        $this->mergeConfigFrom(
            base_path("packages/Hascamp/config/direction.php"), 'direction'
        );
    }

    private function boot_macro(): void
    {
        Request::macro('trackingVisit', function (Request $request, ?Closure $closure = null) {
            app(Accessible::class)
            ->visitDirector($request, $closure);
        });

        Request::macro('direction', function () {
            return app(Accessible::class);
        });
    }

    private function boot_middleware(): void
    {
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', TrackingVisit::class);
        $router->aliasMiddleware('client', VisitorsAsClient::class);
    }
}
