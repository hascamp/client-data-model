<?php

namespace Hascamp\Client\Models;

use Hascamp\Client\Models\Platform;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;

class Business extends DataModel implements Modelable
{
    public function __construct(
        public string|int $id,
        public string $name,
        public ?Platform $platform = null
    )
    {}
}