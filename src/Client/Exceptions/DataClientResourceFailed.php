<?php

namespace Hascamp\Client\Exceptions;

use Hascamp\Exceptions\HascampException;

class DataClientResourceFailed extends HascampException
{
    protected $logLevelDefault = "error";
    protected $logMessageDefault = "Client Data Model Failed.";
}