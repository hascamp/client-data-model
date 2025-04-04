<?php

namespace Hascamp\Client;

use Illuminate\Support\ServiceProvider;
use Hascamp\Client\Request\Requestion;
use Hascamp\Client\Contracts\DataRequest;
use Hascamp\Client\Request\RequestFactory;
use Illuminate\Contracts\Foundation\Application;

class ClientDataModelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/client-modelable.php', 'modelable'
        );

        $this->app->bind(DataRequest::class, function(Application $app) {
            return new RequestFactory(new Requestion($app->config['modelable']));
        });
    }
    
    public function boot(): void
    {
        // $this->publishes([
        //     __DIR__.'/../config/client-modelable.php' => config_path('modelable.php'),
        // ]);
    }
}
