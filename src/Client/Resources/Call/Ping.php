<?php

namespace Hascamp\Client\Resources\Call;

use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;

class Ping extends DataModel implements Modelable
{
    public function __construct(
        public readonly array $connection = [],
        public readonly array $documentations = [],
    )
    {
        parent::__construct();
    }

    protected function visibility(): array
    {
        return [
            'connection',
            'documentations',
        ];
    }

    public function index(): static
    {
        return $this->connectionWithProxy('get', 'ping', $this->requestModel);
    }

    public function trace(): static
    {
        return $this->connection('get', 'ping/trace');
    }
}