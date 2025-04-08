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

    public function isAlready(): bool
    {
        if (! empty($this->id)) {
            return true;
        }

        return false;
    }

    public function toAgent(): string
    {
        if ($this->isAlready()) {
            return str_replace(' ', '', $this->name) . "/{$this->base_license_type_of}-{$this->id}";
        }

        return str_replace(' ', '-', config('app.name')) . "/null-0";
    }
}