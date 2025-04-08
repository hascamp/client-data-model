<?php

namespace Hascamp\Direction\Contracts\Service\Platform;

use Hascamp\Direction\App\Base;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Direction\App\PlatformService;

interface BasePlatform
{
    public function id(): ?string;
    public function key(): ?string;
    public function connection(): ?string;
    public function crypt(): ?string;
    public function pingInitialized(DataModel $ping): static;
    public function base(): Base;
    public function platformService(): PlatformService;
    public function userAgent(): string;
}