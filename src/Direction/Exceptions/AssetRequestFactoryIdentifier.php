<?php

namespace Hascamp\Direction\Exceptions;

use Hascamp\Exceptions\HascampException;

class AssetRequestFactoryIdentifier extends HascampException
{
    protected $logLevelDefault = "error";
    protected $logMessageDefault = "Client App Request Error Identified.";
}