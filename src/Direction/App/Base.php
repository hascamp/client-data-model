<?php

namespace Hascamp\Direction\App;

use Spatie\LaravelData\Data;

class Base extends Data
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $initial = null,
        public readonly ?string $type_of = null,
        public readonly ?string $release_id = null,
        public readonly ?string $version = null,
        public readonly int $vin = 0,
    )
    {}
}