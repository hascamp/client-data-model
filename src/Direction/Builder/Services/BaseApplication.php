<?php

namespace Hascamp\Direction\Builder\Services;

use Closure;
use Hascamp\Direction\App\Base;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Direction\App\PlatformService;
use Hascamp\Direction\Exceptions\AppIdentifier;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;

class BaseApplication implements BasePlatform
{
    private static $id;
    private static $key;
    private static $connection;
    private static $crypt;

    private static Base $base;
    private static PlatformService $platformService;
    
    public function __invoke(?string $id, ?string $key, ?string $connection, ?string $crypt)
    {
        static::$id = encrypt($id);
        static::$key = encrypt($key);
        static::$connection = encrypt($connection);
        static::$crypt = $crypt;

        static::$base = Base::from([]);
        static::$platformService = PlatformService::from([]);

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

    public function pingInitialized(DataModel|array $ping): static
    {
        $originalResults = [];

        if ($ping instanceof DataModel) {
            $originalResults = $ping->successful() ? $ping->getOriginalResults() : null;
        }
        else if (is_array($ping)) {
            $originalResults = $ping;
        }

        if (
            ! isset($originalResults['meta']['base']) &&
            ! isset($originalResults['meta']['platform_service'])
        ) {
            report(new AppIdentifier("Unable to identify client application."));
            abort(403);
        }

        $meta = $originalResults['meta'];

        if ($meta) {
            
            if ($meta['base']['id'] !== $this->id()) {
                report(new AppIdentifier("Unable to identify client application."));
                abort(403);
            }

            logger("BASE ===", $meta['base']);
            logger("PLATFORM_SERVICE ===", $meta['platform_service']);
            static::$base = Base::from($meta['base']);
            static::$platformService = PlatformService::from($meta['platform_service']);
        }

        return $this;
    }

    public function base(): Base
    {
        return static::$base;
    }

    public function platformService(): PlatformService
    {
        return static::$platformService;
    }

    public function userAgent(): string
    {
        return $this->toUserAgentGenerate(static::$base, static::$platformService);
    }

    private function toUserAgentGenerate(Base $base, PlatformService $platformService): ?string
    {
        return (string) "{$base->toAgent()} {$platformService->toAgent()}";
    }
}