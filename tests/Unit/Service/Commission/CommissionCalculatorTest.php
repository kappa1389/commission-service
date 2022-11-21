<?php

namespace Test\Unit\Service\Commission;

use App\Constants\CommissionFee;
use App\DTO\Bin;
use App\DTO\Transaction;
use App\Service\Card\Lookup\BinLookupProviderInterface;
use App\Service\Commission\CommissionCalculator;
use App\Service\Currency\Converter\CurrencyToEuroConverter;
use App\Service\Location\LocationService;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Test\Unit\BaseUnitTestCase;

class CommissionCalculatorTest extends BaseUnitTestCase
{
    public const COUNTRY_CODE = 'fake-code';
    public const AMOUNT_IN_EURO = 100;

    private LegacyMockInterface|LocationService|MockInterface $locationService;
    private CommissionCalculator $sut;
    private Transaction|LegacyMockInterface|MockInterface $transaction;
    private CurrencyToEuroConverter|LegacyMockInterface|MockInterface $currencyToEuroConverter;

    protected function setUp(): void
    {
        parent::setUp();

        $binNumber = 1111;

        $this->transaction = Mockery::mock(Transaction::class);
        $bin = Mockery::mock(Bin::class);
        $binLookupProvider = Mockery::mock(BinLookupProviderInterface::class);
        $this->currencyToEuroConverter = Mockery::mock(CurrencyToEuroConverter::class);
        $this->locationService = Mockery::mock(LocationService::class);

        $this->transaction->expects('getBinNumber')->withNoArgs()->andReturn($binNumber);
        $binLookupProvider->expects('lookup')->with($binNumber)->andReturn($bin);
        $bin->expects('getCountryCode')->withNoArgs()->andReturn(self::COUNTRY_CODE);

        $this->sut = new CommissionCalculator(
            $binLookupProvider,
            $this->currencyToEuroConverter,
            $this->locationService
        );
    }

    public function testShouldReturnCommissionCorrectlyWhenCardIsIssuedInAEuropeanCountry()
    {
        $this->locationService->expects('isEuropeanCountry')->with(self::COUNTRY_CODE)->andReturnTrue();

        $this->currencyToEuroConverter->expects('convert')->with($this->transaction)->andReturn(self::AMOUNT_IN_EURO);

        $actual = $this->sut->calculate($this->transaction);

        $commissionFee = CommissionFee::EURO_COUNTRIES_COMMISSION_FEE;
        $expected = $commissionFee * self::AMOUNT_IN_EURO;

        self::assertEquals($expected, $actual);
    }

    public function testShouldReturnCommissionCorrectlyWhenCardIsIssuedInANonEuropeanCountry()
    {
        $this->locationService->expects('isEuropeanCountry')->with(self::COUNTRY_CODE)->andReturnFalse();

        $this->currencyToEuroConverter->expects('convert')->with($this->transaction)->andReturn(self::AMOUNT_IN_EURO);

        $actual = $this->sut->calculate($this->transaction);

        $commissionFee = CommissionFee::NON_EURO_COUNTRIES_COMMISSION_FEE;
        $expected = $commissionFee * self::AMOUNT_IN_EURO;

        self::assertEquals($expected, $actual);
    }

    public function testShouldRoundUpCommission()
    {
        $this->locationService->expects('isEuropeanCountry')->with(self::COUNTRY_CODE)->andReturnTrue();

        $this->currencyToEuroConverter->expects('convert')->with($this->transaction)->andReturn(121.31247);

        $actual = $this->sut->calculate($this->transaction);

        self::assertEquals(1.22, $actual);
    }
}