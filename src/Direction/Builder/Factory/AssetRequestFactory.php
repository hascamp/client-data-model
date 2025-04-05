<?php

namespace Hascamp\Direction\Builder\Factory;

use Closure;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;
use Hascamp\Direction\Exceptions\AssetRequestFactoryIdentifier;

final class AssetRequestFactory
{
    /** @var \Hascamp\Direction\Builder\Factory\AssetRequestFactory|null */
    private static $self;

    /** @var \Hascamp\Direction\Builder\Factory\AssetRequest */
    private $assets;

    /** @var bool */
    private static $requestPermission = false;

    private function newAssetRequestInstance(BasePlatform $app): void
    {
        static::$self->assets = new AssetRequest($app);
    }

    private function assets(): AssetRequest
    {
        return static::$self->assets;
    }

    public function __invoke(BasePlatform $app, string $routeName, ?string $hspid = null)
    {
        if (static::$self instanceof AssetRequestFactory) {
            throw new AssetRequestFactoryIdentifier();
        }

        static::$self = new self();
        static::$self->newAssetRequestInstance($app);
        static::$self->assets()->createRequestId($routeName, $hspid);
        static::$self->assets()->createTraceId($routeName);

        $this->set_request_permission();

        return static::$self;
    }

    private function set_request_permission(): void
    {
        if (
            static::$self?->assets()?->hasRequestId() &&
            static::$self?->assets()?->hasTraceId()
        ) {
            static::$requestPermission = true;
        }

        else {
            static::$requestPermission = false;
        }
    }

    public function requestPermission(): bool
    {
        $this->set_request_permission();
        return static::$requestPermission;
    }

    public function requestId(): string
    {
        return static::$self?->assets()?->requestId();
    }

    public function traceId(): string
    {
        return static::$self?->assets()?->traceId();
    }

    public function asHeaders(): Closure
    {
        return static::$self->assets()->asHeaders();
    }
}