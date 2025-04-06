<?php

namespace Hascamp\Direction\App;

use Spatie\LaravelData\Data;

class Base extends Data
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = "Ekatalog Marketing Studio",
        public readonly ?string $initial = "EMS",
        public readonly ?string $type_of = "run",
        public readonly ?string $release_id = "0",
        public readonly ?string $version = "0",
        public readonly int $vin = 0,
    )
    {}
}