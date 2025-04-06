<?php

namespace Hascamp\Direction\Contracts\Service\Platform;

use Hascamp\Client\Contracts\DataModel;
use Hascamp\Direction\App\Base;
use Hascamp\Direction\App\PlatformService;

interface BasePlatform
{
    public function environmentBaseMetaChanged(bool $isUpdated): void;
    public function hasMetaIdentified(): bool;
    public function getMetaIdentified(): array;
    public function id(): ?string;
    public function key(): ?string;
    public function connection(): ?string;
    public function crypt(): ?string;
    public function pingInitialized(DataModel $ping): void;
    public function base(): Base;
    public function platformService(): PlatformService;
    public function userAgent(): string;
}