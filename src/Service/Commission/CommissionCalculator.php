<?php

namespace App\Service\Commission;

use App\Constants\CommissionFee;
use App\DTO\Bin;
use App\DTO\Transaction;
use App\Exceptions\CurrencyRateRequestException;
use App\Exceptions\InvalidBinException;
use App\Exceptions\RemoteServerException;
use App\Service\Card\Lookup\BinLookupProviderInterface;
use App\Service\Currency\Converter\CurrencyToEuroConverter;
use App\Service\Location\LocationService;

class CommissionCalculator
{
    public function __construct(
        private BinLookupProviderInterface $binLookupProvider,
        private CurrencyToEuroConverter $currencyToEuroConverter,
        private LocationService $locationService
    ) {
    }

    /**
     * @throws InvalidBinException
     * @throws CurrencyRateRequestException
     * @throws RemoteServerException
     */
    public function calculate(Transaction $transaction): float
    {
        $bin = $this->binLookupProvider->lookup($transaction->getBinNumber());
        $amountInEuro = $this->currencyToEuroConverter->convert($transaction);
        $commissionFee = $this->calculateCommissionFee($bin);

        $commission = $amountInEuro * $commissionFee;

        return $this->round($commission);
    }

    private function calculateCommissionFee(Bin $bin): float
    {
        $isEuropeanCard = $this->locationService->isEuropeanCountry($bin->getCountryCode());

        return $isEuropeanCard
            ?CommissionFee::EURO_COUNTRIES_COMMISSION_FEE
            :CommissionFee::NON_EURO_COUNTRIES_COMMISSION_FEE;
    }

    private function round(float $commission): float
    {
        return ceil($commission * 100) / 100;
    }
}
