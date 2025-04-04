<?php

namespace Hascamp\Client;

use Illuminate\Support\Facades\Facade;
use Hascamp\Client\Contracts\DataRequest;

class Resource extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return DataRequest::class;
    }
}