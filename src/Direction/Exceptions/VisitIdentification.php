<?php

namespace Hascamp\Direction\Exceptions;

use Hascamp\Exceptions\HascampException;

class VisitIdentification extends HascampException
{
    protected $logLevelDefault = "error";
    protected $logMessageDefault = "Failed to identify visit.";
}