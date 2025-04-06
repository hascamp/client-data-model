<?php

namespace Hascamp\Direction\Builder\Factory;

use Closure;
use Hascamp\Direction\Exceptions\VisitIdentification;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;
use Hascamp\Direction\Exceptions\AssetRequestFactoryIdentifier;

final class AssetRequestFactory
{
    /** @var \Hascamp\Direction\Builder\Factory\AssetRequestFactory|null */
    private static $self;

    /** @var \Hascamp\Direction\Builder\Factory\AssetRequest */
    private $assets;

    /** @var bool */
    private $requestPermission = false;

    public function __invoke(BasePlatform $app, ?string $routeName, ?string $hspid)
    {
        if (! static::$self instanceof AssetRequestFactory) {
            static::$self = new self();
        }

        static::$self->assets = new AssetRequest($app);
        static::$self->assets->createRequestId($routeName, $hspid);
        static::$self->assets->createTraceId($routeName);
        $this->set_request_permission();

        return static::$self;
    }

    private function set_request_permission(): void
    {
        if (
            static::$self?->assets?->hasRequestId() &&
            static::$self?->assets?->hasTraceId()
        ) {
            $this->requestPermission = true;
        }

        else {
            $this->requestPermission = false;
        }
    }

    public function requestPermission(): bool
    {
        $this->set_request_permission();
        return $this->requestPermission;
    }

    public function requestId(): string
    {
        return static::$self->assets->requestId();
    }

    public function traceId(): string
    {
        return static::$self->assets->traceId();
    }

    public function asHeaders(): Closure
    {
        return static::$self->assets->asHeaders();
    }
}