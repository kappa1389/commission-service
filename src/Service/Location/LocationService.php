<?php

namespace App\Service\Location;

class LocationService
{
    public function isEuropeanCountry(string $code): bool
    {
        return match ($code) {
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
            'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK' => true,
            default => false,
        };
    }
}
