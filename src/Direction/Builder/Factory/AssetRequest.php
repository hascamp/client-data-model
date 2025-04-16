<?php

namespace Hascamp\Direction\Builder\Factory;

use Hascamp\BaseCrypt\Encryption\BaseCrypt;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;
use Hascamp\Direction\Exceptions\AssetRequestFactoryIdentifier;

class AssetRequest
{
    const REQUEST_ID = "request_id";
    const TRACE_ID = "trace_id";

    protected $stamp;

    public function __construct(
        private BasePlatform $app
    )
    {
        $this->stamp = now()->timestamp;
    }

    private function store(string $key, string $data): void
    {
        session([$key => $data]);
    }

    private function generate(mixed $data): string
    {
        return BaseCrypt::encrypt($data, $this->app->key());
    }

    private function restore(string $data): string
    {
        return BaseCrypt::decrypt($data, $this->app->key());
    }

    public function createRequestId(?string $routeName, ?string $hspid): void
    {
        try {
            $requestId = $this->app->connection();
            $requestId .= "|{$hspid}";
            $requestId .= "|{$routeName}";
            $requestId .= "|{$this->stamp}";
    
            $this->store(static::REQUEST_ID, $this->generate($requestId));
        } catch (\Throwable $th) {
            report(new AssetRequestFactoryIdentifier($th->getMessage()));
        }
    }

    public function createTraceId(?string $routeName): void
    {
        try {
            $traceId = csrf_token();
            $traceId .= "::{$routeName}";
            $traceId .= $this->stamp;

            $this->store(static::TRACE_ID, $traceId); // temporary ...
        } catch (\Throwable $th) {
            report(new AssetRequestFactoryIdentifier($th->getMessage()));
        }
    }

    public function hasRequestId(): bool
    {
        return session()->has(AssetRequest::REQUEST_ID);
    }

    public function hasTraceId(): bool
    {
        return session()->has(AssetRequest::TRACE_ID);
    }

    public function requestId(): string
    {
        if (! $this->hasRequestId()) {
            throw new AssetRequestFactoryIdentifier();
        }

        return session(static::REQUEST_ID, "");
    }

    public function traceId(): string
    {
        if (! $this->hasTraceId()) {
            throw new AssetRequestFactoryIdentifier();
        }

        return session(static::TRACE_ID, "");
    }
}