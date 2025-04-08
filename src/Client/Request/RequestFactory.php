<?php

namespace Hascamp\Client\Request;

use Closure;
use Hascamp\Client\Request\Requestion;
use Hascamp\Client\Contracts\Modelable;
use Hascamp\Client\Contracts\DataRequest;
use Hascamp\Direction\Contracts\Accessible;

final class RequestFactory implements DataRequest
{
    protected static $__requestion;

    public function __construct(
        Requestion $request
    )
    {
        static::$__requestion = $request;
    }

    public function optimize(Accessible $accessible): static
    {
        static::$__requestion->setAccessible($accessible);
        return $this;
    }

    public function data(string $event, array $data = []): Modelable
    {
        return static::$__requestion
                ->process($event)
                ->request($data);
    }
}