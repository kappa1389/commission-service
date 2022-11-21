<?php

namespace App\Service\Currency\Rate;

use App\DTO\Currency;
use App\Exceptions\CurrencyRateRequestException;
use App\Exceptions\RemoteServerException;

interface CurrencyRateProviderInterface
{
    /**
     * @throws CurrencyRateRequestException
     * @throws RemoteServerException
     */
    public function calculateRate(Currency $to, ?Currency $from = null): float;
}
