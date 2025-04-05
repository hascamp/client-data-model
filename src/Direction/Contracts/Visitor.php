<?php

namespace Hascamp\Direction\Contracts;

interface Visitor
{
    public function __set($key, $value);
}