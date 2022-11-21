<?php

namespace Test\Unit\Service\Currency\Rate\Providers;

use App\Constants\Exchangerates;
use App\DTO\Currency;
use App\Exceptions\CurrencyRateRequestException;
use App\Exceptions\RemoteServerException;
use App\Service\Currency\Rate\Providers\Exchangerates\ExchangeratesCurrencyRateProvider;
use App\Service\Http\HttpClient;
use App\Service\Http\Response;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Test\Unit\BaseUnitTestCase;

class ExchangeratesCurrencyRateProviderTest extends BaseUnitTestCase
{
    private LegacyMockInterface|MockInterface|HttpClient $client;
    private ExchangeratesCurrencyRateProvider $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(HttpClient::class);

        $this->sut = new ExchangeratesCurrencyRateProvider($this->client);
    }

    public function testShouldThrowExceptionIfResponseIsNotSuccessful()
    {
        $errorMessage = 'dummy message';

        $response = Mockery::mock(Response::class);
        $response->expects('isSuccessful')->andReturnFalse();
        $response->expects('message')->andReturn($errorMessage);

        $this->client
            ->expects('get')
            ->with(
                sprintf('%s%s', Exchangerates::BASE_URL, Exchangerates::LATEST_RATES_URI),
                [],
                []
            )
            ->andReturn($response);

        self::expectException(RemoteServerException::class);
        self::expectErrorMessage($errorMessage);

        $this->sut->calculateRate(Mockery::mock(Currency::class));
    }

    public function testShouldThrowExceptionIfResponseIsSuccessfulButHasErrors()
    {
        $errorInfo = 'dummy message';
        $body = sprintf('{"success": false, "error": {"info":"%s"}}', $errorInfo);

        $response = Mockery::mock(Response::class);
        $response->expects('isSuccessful')->withNoArgs()->andReturnTrue();
        $response->expects('body')->withNoArgs()->andReturn($body);

        $this->client
            ->expects('get')
            ->with(
                sprintf('%s%s', Exchangerates::BASE_URL, Exchangerates::LATEST_RATES_URI),
                [],
                []
            )
            ->andReturn($response);

        self::expectException(CurrencyRateRequestException::class);
        self::expectErrorMessage($errorInfo);

        $this->sut->calculateRate(Mockery::mock(Currency::class));
    }

    public function testShouldReturnRateCorrectly()
    {
        $usdRate = 1.2;
        $body = sprintf('{"success": true, "rates": {"USD":"%s"}}', $usdRate);

        $response = Mockery::mock(Response::class);
        $response->expects('isSuccessful')->withNoArgs()->andReturnTrue();
        $response->expects('body')->twice()->withNoArgs()->andReturn($body);

        $currency = Mockery::mock(Currency::class);
        $currency->expects('getCode')->withNoArgs()->andReturn('USD');

        $this->client
            ->expects('get')
            ->with(
                sprintf('%s%s', Exchangerates::BASE_URL, Exchangerates::LATEST_RATES_URI),
                [],
                []
            )
            ->andReturn($response);

        $actual = $this->sut->calculateRate($currency);

        self::assertEquals($actual, $usdRate);
    }

    public function testShouldCallApiWithBaseCurrencyWhenFromIsNotNull()
    {
        $usdRate = 1.2;
        $body = sprintf('{"success": true, "rates": {"USD":"%s"}}', $usdRate);

        $response = Mockery::mock(Response::class);
        $response->expects('isSuccessful')->withNoArgs()->andReturnTrue();
        $response->expects('body')->twice()->withNoArgs()->andReturn($body);

        $to = Mockery::mock(Currency::class);
        $to->expects('getCode')->withNoArgs()->andReturn('USD');

        $from = Mockery::mock(Currency::class);
        $from->expects('getCode')->withNoArgs()->andReturn('EUR');

        $this->client
            ->expects('get')
            ->with(
                sprintf('%s%s', Exchangerates::BASE_URL, Exchangerates::LATEST_RATES_URI),
                [],
                ['base' => 'EUR']
            )
            ->andReturn($response);

        $actual = $this->sut->calculateRate($to, $from);

        self::assertEquals($actual, $usdRate);
    }

    public function testShouldCallApiWithAccessKeyWhenAccessKeyIsNotNull()
    {
        $accessKey = 'fake-access-key';
        $usdRate = 1.2;
        $body = sprintf('{"success": true, "rates": {"USD":"%s"}}', $usdRate);

        $response = Mockery::mock(Response::class);
        $response->expects('isSuccessful')->withNoArgs()->andReturnTrue();
        $response->expects('body')->twice()->withNoArgs()->andReturn($body);

        $to = Mockery::mock(Currency::class);
        $to->expects('getCode')->withNoArgs()->andReturn('USD');

        $this->client
            ->expects('get')
            ->with(
                sprintf('%s%s', Exchangerates::BASE_URL, Exchangerates::LATEST_RATES_URI),
                [],
                ['access_key' => $accessKey]
            )
            ->andReturn($response);

        $sut = Mockery::mock(ExchangeratesCurrencyRateProvider::class, [$this->client])->makePartial();
        $sut->expects('getAccessKey')->withNoArgs()->andReturn($accessKey);

        $actual = $sut->calculateRate($to);

        self::assertEquals($actual, $usdRate);
    }
}