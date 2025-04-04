<?php

namespace Hascamp\Client\Models\Auth;

use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;

class Login extends DataModel implements Modelable
{
    public function __construct(
        public readonly bool $logged,
    )
    {}

    public static function user(array $data)
    {
        return static::connection(
            function($request) use ($data) {
                $request
                ->data($data)
                ->method('post')
                ->url('auth/login');
            },
            function($result) {
                return [
                    'logged' => $result['logged'] ?? false,
                ];
            }
        );
    }
}