<?php

namespace Hascamp\Direction\Builder\Master;

use Illuminate\Support\Facades\Config;
use Hascamp\Direction\Exceptions\AppIdentifier;
use Hascamp\Direction\Contracts\Service\Visitable;
use Hascamp\Direction\Exceptions\VisitIdentification;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;
use Hascamp\Direction\Contracts\Service\Requestion;

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
        Config::set('direction', []);
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
            'crypt' => \Illuminate\Support\Str::uuid()->toString()
        ]);
    }

    protected function set_visit_access_permission(bool $access): void
    {
        $this->isPermitted = $access;
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
            throw new VisitIdentification("Users cannot be identified.");
        }

        return true;
    }

    public function app(): BasePlatform
    {
        return $this->app;
    }

    abstract protected function requestion_optimize(): void;

    public function requestion(string $call, string $event = "", array $data = [])
    {
        $request = $this->requestion;
        return $request(request(), $this, $call, $event, $data);
    }
}