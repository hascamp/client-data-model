<?php

namespace Hascamp\Direction\Builder\Factory;

use Hascamp\BaseCrypt\Encryption\BaseCrypt;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;
use Hascamp\Direction\Exceptions\AssetRequestFactoryIdentifier;

class AssetRequest
{
    const REQUEST_ID = "request_id";
    const TRACE_ID = "trace_id";

    public function __construct(
        private BasePlatform $app
    )
    {}

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

    public function createRequestId(string $routeName, ?string $hspid): void
    {
        try {
            $requestId = $this->app->connection();
            $requestId .= "|{$hspid}";
            $requestId .= "|{$routeName}";
            $requestId .= "|" . now()->timestamp;
    
            $this->store(static::REQUEST_ID, $this->generate($requestId));
        } catch (\Throwable $th) {
            report(new AssetRequestFactoryIdentifier($th->getMessage()));
        }
    }

    public function createTraceId(string $context): void
    {
        try {
            $this->store(static::TRACE_ID, csrf_token() . "::{$context}::" . now()->timestamp); // temporary ...
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

        return session(static::REQUEST_ID);
    }

    public function traceId(): string
    {
        if (! $this->hasTraceId()) {
            throw new AssetRequestFactoryIdentifier();
        }

        return session(static::TRACE_ID);
    }

    public function asHeaders(): \Closure
    {
        try {
            $requestId = $this->requestId();
            $traceId = $this->traceId();
        } catch (\Throwable $th) {
            report(new AssetRequestFactoryIdentifier());
            $requestId = null;
            $traceId = null;
        }
        
        return function () use ($requestId, $traceId) {
            return [
                'User-Agent' => $this->app->userAgent(),
                'X-App-ID' => $this->app->id(),
                'X-Request-ID' => $requestId,
                'X-Trace-ID' => $traceId,
                'Authorization' => "Bearer 123456789",
            ];
        };
    }
}