<?php

namespace Hascamp\Client\Contracts;

use Closure;
use Hascamp\Client\Contracts\Modelable;

interface DataRequest
{
    public function optimize(Closure $headers): void;
    public function data(string $event, array $data = []): Modelable;
}