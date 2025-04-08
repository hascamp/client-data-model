<?php

namespace Hascamp\Client\Contracts;

use Hascamp\Client\Contracts\Modelable;
use Hascamp\Direction\Contracts\Accessible;

interface DataRequest
{
    public function optimize(Accessible $accessible): static;
    public function data(string $event, array $data = []): Modelable;
}