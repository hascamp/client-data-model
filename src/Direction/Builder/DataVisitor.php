<?php

namespace Hascamp\Direction\Builder;

use Hascamp\Direction\Supports\IgnoreChanges;
use Hascamp\Direction\Contracts\Visitor as ContractsVisitor;
use Spatie\LaravelData\Data;

final class DataVisitor extends Data implements ContractsVisitor
{
    use IgnoreChanges;
    
    public function __construct(
        public ?string $hspid = null,
        public ?string $name = null,
        public ?string $username = null,
    )
    {}
}