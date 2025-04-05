<?php

namespace Hascamp\Client\Resources\Call;

use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;

class Ping extends DataModel implements Modelable
{
    public function __construct(
        public readonly array $connection = [],
        public readonly array $documentations = [],
        public readonly array $base = [],
        public readonly array $platform_service = [],
    )
    {
        parent::__construct();
    }

    public function index(): static
    {
        return $this->connection('get', 'ping');
    }
}