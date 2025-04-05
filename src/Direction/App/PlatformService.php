<?php

namespace Hascamp\Direction\App;

use Spatie\LaravelData\Data;

class PlatformService extends Data
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $base_license_type_of = null,
        public readonly ?bool $is_active = null,
    )
    {}
}