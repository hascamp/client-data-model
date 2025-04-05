<?php

namespace Hascamp\Direction\App;

use Spatie\LaravelData\Data;

class PlatformService extends Data
{
    public function __construct(
        public readonly string $id = "psid:1",
        public readonly string $name = "Entriplus",
        public readonly string $base_license_type_of = "MOL",
        public readonly bool $is_active = true,
    )
    {}
}