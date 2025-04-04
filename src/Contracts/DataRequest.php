<?php

namespace Hascamp\Client\Contracts;

use Hascamp\Client\Contracts\Modelable;

interface DataRequest
{
    public function data(string $event, array $data = []): Modelable;
}