<?php

namespace Hascamp\Direction\Contracts\Service;

use Hascamp\Direction\Contracts\Accessible;

interface Requestion
{
    public function __invoke(Accessible $accessible, string $call, string $event, array $data);
}