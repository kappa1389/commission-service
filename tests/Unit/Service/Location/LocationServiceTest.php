<?php

namespace Test\Unit\Service\Location;

use App\Service\Location\LocationService;
use Test\Unit\BaseUnitTestCase;

class LocationServiceTest extends BaseUnitTestCase
{
    /**
     * @dataProvider codeProvider
     */
    public function testShouldAssertThatCountryCodeIsEuropeanOrNot(string $code, bool $isEuropean)
    {
        $sut = new LocationService();

        self::assertEquals($isEuropean, $sut->isEuropeanCountry($code));
    }

    public function codeProvider(): array
    {
        return [
            ['code' => 'AT', 'isEuropean' => true],
            ['code' => 'BE', 'isEuropean' => true],
            ['code' => 'BG', 'isEuropean' => true],
            ['code' => 'CY', 'isEuropean' => true],
            ['code' => 'CZ', 'isEuropean' => true],
            ['code' => 'DE', 'isEuropean' => true],
            ['code' => 'DK', 'isEuropean' => true],
            ['code' => 'EE', 'isEuropean' => true],
            ['code' => 'ES', 'isEuropean' => true],
            ['code' => 'FI', 'isEuropean' => true],
            ['code' => 'FR', 'isEuropean' => true],
            ['code' => 'GR', 'isEuropean' => true],
            ['code' => 'HR', 'isEuropean' => true],
            ['code' => 'HU', 'isEuropean' => true],
            ['code' => 'IE', 'isEuropean' => true],
            ['code' => 'IT', 'isEuropean' => true],
            ['code' => 'LT', 'isEuropean' => true],
            ['code' => 'LU', 'isEuropean' => true],
            ['code' => 'LV', 'isEuropean' => true],
            ['code' => 'MT', 'isEuropean' => true],
            ['code' => 'NL', 'isEuropean' => true],
            ['code' => 'PO', 'isEuropean' => true],
            ['code' => 'PT', 'isEuropean' => true],
            ['code' => 'RO', 'isEuropean' => true],
            ['code' => 'SE', 'isEuropean' => true],
            ['code' => 'SI', 'isEuropean' => true],
            ['code' => 'SK', 'isEuropean' => true],
            ['code' => 'not-european', 'isEuropean' => false],
        ];

    }
}