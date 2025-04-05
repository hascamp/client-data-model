<?php

namespace Hascamp\Direction\Supports;

trait IgnoreChanges
{
    public function __set($key, $value) {}
}