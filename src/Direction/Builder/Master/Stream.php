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
use Hascamp\Direction\Builder\Factory\AssetRequestFactory;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;

abstract class Stream
{
    /** @var array */
    protected static $config;
    
    /** @var \Hascamp\Direction\Contracts\Service\Platform\BasePlatform */
    protected $app;

    /** @var bool */
    protected $isPermitted = false;

    /** @var string */
    protected $requestion;

    /** @var \Hascamp\Direction\Contracts\Service\Visitable */
    protected $visit;

    /** @var \Hascamp\Direction\Builder\Factory\AssetRequestFactory */
    protected static $factory;

    public function __construct(
        string $requestion,
        array $config
    )
    {
        $this->requestion = $requestion;
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

    protected function instanceRequest(): Requestion
    {
        $instanceRequest = new $this->requestion;

        if (! $instanceRequest instanceof Requestion) {
            throw new RequestionFailed("Unable to handle client request.");
        }

        // $instanceRequest->setHeader($this->getFactory()->asHeaders());
        return $instanceRequest;
    }

    protected function setFactory(): void
    {
        $factory = new AssetRequestFactory;
        static::$factory = $factory($this->app, $this->visit);
    }

    public function getFactory(): AssetRequestFactory
    {
        return static::$factory;
    }

    protected function optimize_request_preparation(): void
    {
        if ($this->request(call:'setHeaderToResource')) {
            $this->app->pingInitialized($this->request('call.ping:index'));
        }
    }

    public function app(): BasePlatform
    {
        return $this->app;
    }

    public function visit(): Visitable
    {
        return $this->visit;
    }

    public function request(string $event = "", array $data = [], string $call = "resource"): DataModel|bool
    {
        $request = $this->instanceRequest();
        return $request($this, $call, $event, $data);
    }
}