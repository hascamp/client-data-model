<?php

namespace Hascamp\Direction\Builder\Services;

use Closure;
use Hascamp\Direction\App\Base;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Direction\App\PlatformService;
use Hascamp\Direction\Exceptions\AppIdentifier;
use Hascamp\Direction\Supports\BaseMetaIdentified;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;

class BaseApplication implements BasePlatform
{
    use BaseMetaIdentified;

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

    public function pingInitialized(DataModel|array $ping): void
    {
        $originalResults = null;

        if ($ping instanceof DataModel) {
            $originalResults = $ping->successful() ? $ping->getOriginalResults() : null;
        }
        else if (is_array($ping)) {
            $originalResults = $ping;
        }

        $meta = $this->meta_identified($originalResults);
        if ($meta) {
            
            if ($meta['base']['id'] !== $this->id()) {
                report(new AppIdentifier("Unable to identify client application."));
                $this->reset_meta_identified();
                abort(403);
            }

            static::$base = Base::from($meta['base']);
            static::$platformService = PlatformService::from($meta['platform_service']);
        }
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
        return $this->user_agent_generate(static::$base, static::$platformService);
    }

    private function user_agent_generate(Base $base, PlatformService $platformService): ?string
    {
        $result = $base->initial;
        $result .= "/{$base->version}";
        $result .= " (Released:{$base->release_id}) ";
        $result .= str_replace(' ', '', $platformService->name) . "/{$platformService->base_license_type_of}";

        return (string) $result;
    }
}