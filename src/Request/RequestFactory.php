<?php

namespace Hascamp\Client\Request;

use Hascamp\Client\Request\Requestion;
use Hascamp\Client\Contracts\Modelable;
use Hascamp\Client\Contracts\DataRequest;

final class RequestFactory implements DataRequest
{
    protected static $__requestion;

    public function __construct(
        Requestion $request
    )
    {
        static::$__requestion = $request;
    }

    public function data(string $event, array $data = []): Modelable
    {
        return static::$__requestion
                ->process($event)
                ->request($data, function($factory) {
                    return $factory->method();
                });
    }
}