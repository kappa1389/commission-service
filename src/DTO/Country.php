<?php

namespace App\DTO;

class Country
{
    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
