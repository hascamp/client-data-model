<?php

namespace Hascamp\Direction\App;

use Spatie\LaravelData\Data;

class Base extends Data
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = "Ekatalog Marketing Studio",
        public readonly ?string $initial = "EMS",
        public readonly ?string $type_of = "call.ping",
        public readonly ?string $release_id = null,
        public readonly ?string $version = "Ping",
        public readonly int $vin = 0,
    )
    {}

    public function isAlready(): bool
    {
        if (! empty($this->id)) {
            return true;
        }

        return false;
    }

    public function toAgent(): string
    {
        return "{$this->initial}/{$this->version} ({$this->type_of})";
    }
}