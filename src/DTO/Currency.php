<?php

namespace App\DTO;

class Currency
{
    public function __construct(protected string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
