<?php

namespace Hascamp\Direction\Supports;

use Hascamp\Client\Resource;
use Hascamp\Client\Contracts\DataModel;

trait CallableRequest
{
    private function setHeaderToResource(): void
    {
        Resource::optimize($this->headers());
    }

    private function ping(string $event, array $data): DataModel
    {
        return Resource::data($event, $data);
    }
}