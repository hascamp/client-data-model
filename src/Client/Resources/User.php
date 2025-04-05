<?php

namespace Hascamp\Client\Resources;

use Closure;
use Illuminate\Support\Facades\DB;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;
use Hascamp\Client\Resources\UserClient;

class User extends DataModel implements Modelable
{
    public function __construct(
        public string|int $id,
        public string $name,
        public readonly bool $isVerified = false,
        public readonly ?UserClient $panel = null,
    )
    {}

    public static function fromUser(string|int|null $id, ?Closure $request = null): ?self
    {
        $result = null;
        if(! empty($id)) {
            $user = DB::table('users')->find($id);
            if($request instanceof Closure) {
                $result = $request($user);
            }
        }

        return $result;
    }

    public static function me(): self
    {
        $user = static::from(['id' => 1, 'name' => "Examle Spatie Data", 'isVerified' => true]);
        return $user;
    }
}