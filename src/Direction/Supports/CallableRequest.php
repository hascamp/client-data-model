<?php

namespace Hascamp\Direction\Supports;

use Closure;
use Hascamp\Client\Resource;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Direction\Exceptions\RequestionFailed;
use Throwable;

trait CallableRequest
{
    private function setHeaderToResource(): bool
    {
        $headers = $this->headers();

        try {
            if ($headers instanceof Closure || is_array($headers)) {
                Resource::optimize($headers);
                return true;
            }
        } catch (Throwable $e) {
            report(new RequestionFailed($e->getMessage()));
        }

        return false;
    }

    private function resource(string $event, array $data): DataModel
    {
        return Resource::data($event, $data);
    }
}