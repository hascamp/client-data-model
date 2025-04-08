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

    public function toAgent(): string
    {
        if (! empty($this->id)) {
            return str_replace(' ', '', $this->name) . "/{$this->id}-{$this->base_license_type_of}";
        }

        return str_replace(' ', '-', config('app.name')) . "/0-null";
    }
}