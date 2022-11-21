<?php

namespace App\Exceptions;

use Exception;

class InvalidBinException extends Exception
{
    protected $code = 400;
}