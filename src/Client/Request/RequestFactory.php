<?php

namespace Hascamp\Client\Request;

use Closure;
use Hascamp\Client\Request\Requestion;
use Hascamp\Client\Contracts\Modelable;
use Hascamp\Client\Contracts\DataRequest;

final class RequestFactory implements DataRequest
{
    protected static $__requestion;

    /** @var \Closure|array */
    protected static $headers = [];

    public function __construct(
        Requestion $request
    )
    {
        static::$__requestion = $request;
    }

    public function optimize(Closure|array $headers): static
    {
        static::$headers = $headers;
        return $this;
    }

    public function data(string $event, array $data = []): Modelable
    {
        $headers = static::$headers;

        if ($headers instanceof Closure) {
            $headers = $headers();
        }

        return static::$__requestion
                ->process($event)
                ->request($data, $headers);
    }
}