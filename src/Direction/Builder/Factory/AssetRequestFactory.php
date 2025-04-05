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
    private static $requestPermission = false;

    public function __invoke(BasePlatform $base, string $routeName, ?string $hspid)
    {
        if (static::$self instanceof AssetRequestFactory) {
            report(new AssetRequestFactoryIdentifier("Instantiating ".__CLASS__." more than once."));
            throw new VisitIdentification(
                "The request cannot be continued. The visitor is suspected of violating the access policy by exceeding the allowed request limit.",
                403,
                [
                    "policy" => "Duplicate requests for instant access in the same cycle."
                ]
            );
        }

        static::$self = new self();
        static::$self->assets = new AssetRequest($base);
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