<?php

namespace Hascamp\Direction\Contracts\Service;

interface Requestion
{
    public function setHeader(\Closure $headers): void;
}