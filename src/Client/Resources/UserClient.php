<?php

namespace Hascamp\Client\Resources;

use Spatie\LaravelData\Optional;
use Hascamp\Client\Resources\Business;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;

class UserClient extends DataModel implements Modelable
{
    public function __construct(
        public string|int $id,
        public string $name,
        public Business|Optional $business,
    )
    {}
}