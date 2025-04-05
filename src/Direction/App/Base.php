<?php

namespace Hascamp\Direction\App;

use Spatie\LaravelData\Data;

class Base extends Data
{
    public function __construct(
        public readonly string $id = "xxxx-xxxx-xxxx-xxxx",
        public readonly string $name = "Ekatalog Marketing Studio",
        public readonly string $initial = "EMS",
        public readonly string $type_of = "web_based",
        public readonly string $release_id = "1",
        public readonly string $version = "1.0.0",
        public readonly bool $is_active = true,
    )
    {}
}