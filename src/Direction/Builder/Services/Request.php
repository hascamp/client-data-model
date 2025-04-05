<?php

namespace Hascamp\Direction\Builder\Services;

use Closure;
use Hascamp\Direction\Contracts\Accessible;
use Illuminate\Http\Request as HttpRequest;
use Hascamp\Direction\Supports\CallableRequest;
use Hascamp\Direction\Exceptions\RequestionFailed;
use Hascamp\Direction\Contracts\Service\Requestion;

class Request implements Requestion
{
    use CallableRequest;
    
    /** @var \Illuminate\Http\Request */
    protected static $http;

    /** @var \Hascamp\Direction\Contracts\Accessible */
    protected static $accessible;

    /** @var \Closure */
    protected $headers = [];

    public function setHeader(Closure $headers): void
    {
        $this->headers = $headers;
    }

    private function headers(): Closure
    {
        return $this->headers;
    }

    public function __invoke(HttpRequest $request, Accessible $accessible, string $call, string $event, array $data)
    {
        if (! method_exists($this, $call)) {
            throw new RequestionFailed("{$call} not Found.");
        }

        try {
            static::$http = $request;
            static::$accessible = $accessible;
            return $this->{$call}($event, $data);
        } finally {
            $this->presets();
        }
    }

    private function presets(): void
    {
        static::$http = null;
        static::$accessible = null;
    }
}