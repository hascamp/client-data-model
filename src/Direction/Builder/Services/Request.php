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

    /** @var \Closure|array */
    protected $headers = [];

    public function setHeader(Closure $headers): void
    {
        $this->headers = $headers;
    }

    private function headers(): Closure|array
    {
        return $this->headers;
    }

    public function __invoke(Accessible $accessible, string $call, string $event, array $data)
    {
        if (! method_exists($this, $call)) {
            throw new RequestionFailed("{$call} not Found.");
        }

        try {
            $this->accessible = $accessible;
            return $this->{$call}($event, $data);
        } finally {
            $this->accessible = null;
        }
    }

    private function setHeaderToResource(): bool
    {
        try {
            $dataRequest = Resource::optimize($this->headers());
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