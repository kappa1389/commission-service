<?php

namespace App\Exceptions;

use Exception;

class CurrencyRateRequestException extends Exception
{
    protected $code = 400;
}