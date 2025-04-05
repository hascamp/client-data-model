<?php

namespace Hascamp\Direction\Contracts\Service\Platform;

interface BasePlatform
{
    public function id(): ?string;
    public function key(): ?string;
    public function connection(): ?string;
    public function crypt(): ?string;
    public function base(): array;
    public function service(): array;
    public function userAgent(): string;
}