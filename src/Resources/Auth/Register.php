<?php

namespace Hascamp\Client\Models\Auth;

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

    public static function newUser(array $data): static
    {
        return static::connection(
            function($request) use ($data) {
                $request
                ->data($data)
                ->method('post')
                ->url('auth/register');
            },
            function($results) {
                return [
                    'user' => isset($results['user']) ? $results['user'] : null,
                ];
            }
        );
    }
}