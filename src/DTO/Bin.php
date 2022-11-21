<?php

namespace App\DTO;

class Bin
{
    public function __construct(private Country $country)
    {
    }

    public function getCountryCode(): string
    {
        return $this->country->getCode();
    }
}
