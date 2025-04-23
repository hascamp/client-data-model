<?php

namespace Hascamp\Client\Resources\Auth;

use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;

class Register extends DataModel implements Modelable
{
    public function __construct(
        public readonly ?array $user = null,
    )
    {
        parent::__construct();
    }

    protected function visibility(): array
    {
        return [
            'user',
        ];
    }

    public function newUser(): static
    {
        return $this->connection('post', 'auth/register');
    }
}