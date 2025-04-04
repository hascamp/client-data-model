<?php

namespace Hascamp\Direction\Builder\Master;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Direction\Exceptions\AppIdentifier;
use Hascamp\Direction\Contracts\Service\Visitable;
use Hascamp\Direction\Exceptions\RequestionFailed;
use Hascamp\Direction\Contracts\Service\Requestion;
use Hascamp\Direction\Exceptions\VisitIdentification;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;

abstract class Stream
{
    /** @var array */
    protected static $config;
    
    /** @var \Hascamp\Direction\Contracts\Service\Platform\BasePlatform */
    protected $app;

    /** @var bool */
    protected $isPermitted = false;

    public function __construct(
        protected readonly Requestion $requestion,
        array $config
    )
    {
        $this->set_config($config);
        $this->has_client_environment($config);
    }

    private function set_config(array $config): void
    {
        static::$config = $config;
        Config::set('direction', []); // reset
    }

    protected function has_client_environment(array $config): void
    {
        $app = new $config['services']['app'];
        if (! $app instanceof BasePlatform) {
            throw new AppIdentifier("The client application could not be recognized. There is no Base-Platform Interface match.");
        }

        $this->app = $app(...[
            'id' => $config['app_id'] ?? null,
            'key' => $config['license_key'] ?? null,
            'connection' => $config['connection'] ?? null,
            'crypt' => Str::uuid()->toString()
        ]);
    }

    protected function set_visit_access_permission(bool $access): void
    {
        $this->isPermitted = $access;
    }

    public function visitPermission(): bool
    {
        return $this->isPermitted;
    }

    protected function ensure_visits($visit): bool
    {
        if (! $visit instanceof Visitable) {
            throw new VisitIdentification("Users cannot be identified.");
        }

        return true;
    }

    protected function ensure_request($requestion): bool
    {
        if (! $requestion instanceof Requestion) {
            throw new RequestionFailed("Unable to handle client request.");
        }

        return true;
    }

    protected function optimize_request_preparation(Closure $headers, Closure $app): void
    {
        $this->ensure_request($this->requestion);
        $this->requestion->setHeader($headers);

        $hasDataModel = function (bool $has) {
            if ($has) {
                return $this->app->getMetaIdentified();
            }
            return $this->request('call.ping:index');
        };

        if ($this->request(call:'setHeaderToResource')) {
            $this->app->pingInitialized(
                $hasDataModel($this->app->hasMetaIdentified()),
                $app
            );
        }
    }

    public function app(): BasePlatform
    {
        return $this->app;
    }

    public function request(string $event = "", array $data = [], string $call = "resource"): DataModel|bool
    {
        $this->ensure_request($this->requestion);
        $request = $this->requestion;
        return $request(request(), $this, $call, $event, $data);
    }
}