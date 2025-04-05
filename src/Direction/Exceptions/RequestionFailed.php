<?php

namespace Hascamp\Direction\Exceptions;

use Hascamp\Exceptions\HascampException;

class RequestionFailed extends HascampException
{
    protected $logLevelDefault = "error";
    protected $logMessageDefault = "Requestion error.";
}