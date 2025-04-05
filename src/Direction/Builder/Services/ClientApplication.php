<?php

namespace Hascamp\Direction\Builder\Services;

use Hascamp\Direction\App\Base;
use Hascamp\Direction\App\PlatformService;
use Hascamp\Direction\Exceptions\AppIdentifier;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;

class ClientApplication implements BasePlatform
{
    private static Base $base;
    private static PlatformService $appService;
    private static $id;
    private static $key;
    private static $connection;
    private static $crypt;

    public function __invoke(string $id, string $key, string $connection, string $crypt)
    {
        static::$id = encrypt($id);
        static::$key = encrypt($key);
        static::$connection = encrypt($connection);
        static::$crypt = $crypt;

        static::$base = Base::from([]);
        static::$appService = PlatformService::from([]);

        return $this;
    }

    public function id(): ?string
    {
        try {
            return decrypt(static::$id);
        } catch (\Throwable $th) {
            report(new AppIdentifier($th->getMessage()));
        }

        return null;
    }

    public function key(): ?string
    {
        try {
            return decrypt(static::$key);
        } catch (\Throwable $th) {
            report(new AppIdentifier($th->getMessage()));
        }

        return null;
    }

    public function connection(): ?string
    {
        try {
            return decrypt(static::$connection);
        } catch (\Throwable $th) {
            report(new AppIdentifier($th->getMessage()));
        }

        return null;
    }

    public function crypt(): ?string
    {
        try {
            return decrypt(static::$crypt);
        } catch (\Throwable $th) {
            report(new AppIdentifier($th->getMessage()));
        }

        return null;
    }

    public function base(): array
    {
        return static::$base->toArray();
    }

    public function service(): array
    {
        return static::$appService->toArray();
    }

    public function userAgent(): string
    {
        $base = static::$base;
        $app = static::$appService;
        $appName = str_replace(' ', '', $app->name);
        return "{$base->initial}/{$base->version} Released:{$base->release_id} {$appName}/{$app->base_license_type_of}";
    }
}