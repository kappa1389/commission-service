<?php

namespace App\DTO;

class Transaction
{
    public function __construct(private int $binNumber, private int $amount, private Currency $currency)
    {
    }

    public static function of(int $binNumber, int $amount, string $currencyCode): self
    {
        return new self(
            $binNumber,
            $amount,
            new Currency($currencyCode)
        );
    }

    public function getBinNumber(): int
    {
        return $this->binNumber;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCurrencyCode(): string
    {
        return $this->currency->getCode();
    }
}
