<?php

namespace Hascamp\Client\Resources;

use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;

class Platform extends DataModel implements Modelable
{
    public function __construct(
        public string|int $id,
        public string $typeOf,
        public string $name,
        public string $slug,
        public bool $isVerified,
    )
    {}
}