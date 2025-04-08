<?php

namespace Hascamp\Direction\Builder\Services;

use Closure;
use Hascamp\Client\Resource;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\DataRequest;
use Hascamp\Direction\Contracts\Accessible;
use Hascamp\Direction\Exceptions\RequestionFailed;
use Hascamp\Direction\Contracts\Service\Requestion;

class Request implements Requestion
{
    /** @var \Hascamp\Direction\Contracts\Accessible */
    protected $accessible;

    public function __invoke(Accessible $accessible, string $call, string $event, array $data)
    {
        if (! method_exists($this, $call)) {
            throw new RequestionFailed("{$call} not Found.");
        }

        $this->accessible = $accessible;
        return $this->{$call}($event, $data);
    }

    public function asHeaders(): Closure
    {
        return function () {
            return [
                'User-Agent' => $this->accessible->app()->userAgent(),
                'X-App-ID' => $this->accessible->app()->id(),
                'X-Request-ID' => $this->accessible->getFactory()->requestId(),
                'X-Trace-ID' => $this->accessible->getFactory()->traceId(),
                'Authorization' => "Bearer 123456789",
            ];
        };
    }

    private function setHeaderToResource(): bool
    {
        try {
            $dataRequest = Resource::optimize($this->asHeaders());
            return $dataRequest instanceof DataRequest;
        } catch (\Throwable $e) {
            report(new RequestionFailed($e->getMessage()));
        }

        return false;
    }

    private function resource(string $event, array $data): DataModel
    {
        return Resource::data($event, $data);
    }
}