<?php

namespace Hascamp\Direction\Builder;

use Hascamp\Direction\Supports\IgnoreChanges;
use Hascamp\Direction\Contracts\Visited as ContractsVisited;
use Spatie\LaravelData\Data;

final class DataVisited extends Data implements ContractsVisited
{
    use IgnoreChanges;

    public function __construct(
        public ?string $target = null,
        public ?string $targetId = null,
        public ?string $visitAs = null,
        public ?string $visitAsId = null,
        public ?string $visitRoleAs = null,
    )
    {}
}