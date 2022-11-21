<?php

use App\Dictionary\CurrencyCode;
use App\DTO\Currency;
use App\DTO\Transaction;
use App\Service\Currency\Converter\CurrencyToEuroConverter;
use App\Service\Currency\Rate\CurrencyRateProviderInterface;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Test\Unit\BaseUnitTestCase;

class CurrencyToEuroConverterTest extends BaseUnitTestCase
{
    public const TRANSACTION_AMOUNT = 100;

    private Transaction|LegacyMockInterface|MockInterface $transaction;
    private LegacyMockInterface|CurrencyRateProviderInterface|MockInterface $exchangeRateProvider;
    private CurrencyToEuroConverter $sut;
    private Currency|LegacyMockInterface|MockInterface $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transaction = Mockery::mock(Transaction::class);
        $this->exchangeRateProvider = Mockery::mock(CurrencyRateProviderInterface::class);
        $this->currency = Mockery::mock(Currency::class);
        $this->sut = new CurrencyToEuroConverter($this->exchangeRateProvider);

        $this->transaction->expects('getCurrency')->withNoArgs()->andReturn($this->currency);
        $this->transaction->expects('getAmount')->withNoArgs()->andReturn(self::TRANSACTION_AMOUNT);

    }

    public function testShouldReturnAmountCorrectlyWhenCurrencyIsInEuroAndRateIsZero()
    {
        $rate = 0;
        $this->exchangeRateProvider->expects('calculateRate')->with($this->currency)->andReturn($rate);
        $this->transaction->expects('getCurrencyCode')->withNoArgs()->andReturn(CurrencyCode::EURO);

        $actual = $this->sut->convert($this->transaction);

        self::assertEquals(self::TRANSACTION_AMOUNT, $actual);
    }

    public function testShouldReturnAmountCorrectlyWhenCurrencyIsNotInEuroAndRateIsNotZero()
    {
        $rate = 2;
        $this->exchangeRateProvider->expects('calculateRate')->with($this->currency)->andReturn($rate);
        $this->transaction->expects('getCurrencyCode')->withNoArgs()->andReturn('dummy-code');

        $actual = $this->sut->convert($this->transaction);

        $expected = self::TRANSACTION_AMOUNT / $rate;

        self::assertEquals($expected, $actual);
    }

    public function testShouldThrowExceptionWhenCurrencyIsNotInEuroAndRateIsZero()
    {
        $rate = 0;
        $this->exchangeRateProvider->expects('calculateRate')->with($this->currency)->andReturn($rate);
        $this->transaction->expects('getCurrencyCode')->withNoArgs()->andReturn('dummy-code');

        self::expectException(DivisionByZeroError::class);

        $this->sut->convert($this->transaction);
    }

    public function testShouldReturnAmountCorrectlyWhenCurrencyIsInEuroAndRateIsNotZero()
    {
        $rate = 2;
        $this->exchangeRateProvider->expects('calculateRate')->with($this->currency)->andReturn($rate);
        $this->transaction->expects('getCurrencyCode')->withNoArgs()->andReturn('dummy-code');

        $actual = $this->sut->convert($this->transaction);

        $expected = self::TRANSACTION_AMOUNT / $rate;

        self::assertEquals($expected, $actual);
    }
}