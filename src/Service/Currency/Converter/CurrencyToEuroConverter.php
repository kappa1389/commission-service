<?php

namespace App\Service\Currency\Converter;

use App\Dictionary\CurrencyCode;
use App\DTO\Transaction;
use App\Exceptions\CurrencyRateRequestException;
use App\Exceptions\RemoteServerException;
use App\Service\Currency\Rate\CurrencyRateProviderInterface;

class CurrencyToEuroConverter
{
    public function __construct(
        private CurrencyRateProviderInterface $exchangeRateProvider
    ) {
    }

    /**
     * @throws CurrencyRateRequestException
     * @throws RemoteServerException
     */
    public function convert(Transaction $transaction): float
    {
        $rate = $this->exchangeRateProvider->calculateRate($transaction->getCurrency());

        // This part will cause DivisionByZero exception if currency is not
        // present in provider's response and should be handled,
        // but I didn't handle it to be consistent with
        // original code(it happens there too!)
        if ($transaction->getCurrencyCode() !== CurrencyCode::EURO || $rate > 0.0) {
            return $transaction->getAmount() / $rate;
        }

        return $transaction->getAmount();
    }
}
