<?php

namespace Hascamp\Direction\Builder\Services;

use Hascamp\Client\Resource;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\DataRequest;
use Hascamp\Direction\Contracts\Accessible;
use Hascamp\Direction\Exceptions\RequestionFailed;
use Hascamp\Direction\Contracts\Service\Requestion;

class Request implements Requestion
{
    /** @var \Hascamp\Direction\Contracts\Accessible */
    protected $accessible;

    public function __invoke(Accessible $accessible, string $call, string $event, array $data)
    {
        if (! method_exists($this, $call)) {
            throw new RequestionFailed("{$call} not Found.");
        }

        $this->accessible = $accessible;
        return $this->{$call}($event, $data);
    }

    private function resourceOptimized(): bool
    {
        return Resource::optimize($this->accessible) instanceof DataRequest;
    }

    private function resource(string $event, array $data): DataModel
    {
        return Resource::data($event, $data);
    }
}