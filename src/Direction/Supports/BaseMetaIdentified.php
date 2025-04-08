<?php

namespace Hascamp\Direction\Supports;

use Hascamp\Direction\Exceptions\AppIdentifier;

trait BaseMetaIdentified
{
    private const META_IDENTIFIED = "_BASE_META_IDENTIFIED";
    private static $_UPDATE = false;

    private function meta_identified(array $originalResults): array
    {
        if (static::$_UPDATE) $this->reset_meta_identified();

        if ($this->hasMetaIdentified()) {
            return session(static::META_IDENTIFIED);
        }

        if (
            ! isset($originalResults['meta']['base']) &&
            ! isset($originalResults['meta']['platform_service'])
        ) {
            report(new AppIdentifier("Unable to identify client application."));
            abort(403);
        }

        $newData = [
            'base' => $originalResults['meta']['base'] ?? [],
            'platform_service' => $originalResults['meta']['platform_service'] ?? [],
        ];

        session([static::META_IDENTIFIED => $newData]);
        return $newData;
    }

    private function reset_meta_identified(): void
    {
        session()->forget(static::META_IDENTIFIED);
    }

    public function environmentBaseMetaChanged(bool $isUpdated): void
    {
        static::$_UPDATE = $isUpdated;
    }

    public function hasMetaIdentified(): bool
    {
        return session()->has(static::META_IDENTIFIED);
    }

    public function getMetaIdentified(): array
    {
        return session(static::META_IDENTIFIED);
    }
}