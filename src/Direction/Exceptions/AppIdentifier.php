<?php

namespace Hascamp\Direction\Exceptions;

use Hascamp\Exceptions\HascampException;

class AppIdentifier extends HascampException
{
    protected $logLevelDefault = "error";
    protected $logMessageDefault = "Client application error.";
}