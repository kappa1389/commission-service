<?php

namespace Test\Integration\Service\Commission;

use App\Constants\Binlist;
use App\Constants\CommissionFee;
use App\Constants\Exchangerates;
use App\DTO\Transaction;
use App\Service\Card\Lookup\Providers\BinlistProvider\BinlistBinLookupProvider;
use App\Service\Commission\CommissionCalculator;
use App\Service\Currency\Converter\CurrencyToEuroConverter;
use App\Service\Currency\Rate\Providers\Exchangerates\ExchangeratesCurrencyRateProvider;
use App\Service\Http\HttpClient;
use App\Service\Location\LocationService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Test\Integration\BaseIntegrationTestCase;

class CommissionCalculatorTest extends BaseIntegrationTestCase
{
    public function testShouldWorkCorrectly()
    {
        $binNumber = 1234;
        $amount = 100;
        $currency = 'USD';
        $countryCode = 'dummy-code';
        $usdRate = 1.2;
        $binResponseBody = sprintf('{"country":{"alpha2":"%s"}}', $countryCode);
        $rateResponseBody = sprintf('{"success": true, "rates": {"USD":"%s"}}', $usdRate);

        $client = Mockery::mock(Client::class);
        $binLookupProvider = new BinlistBinLookupProvider(new HttpClient($client));
        $currencyToEuroConverter = new CurrencyToEuroConverter(new ExchangeratesCurrencyRateProvider(new HttpClient($client)));
        $sut = new CommissionCalculator($binLookupProvider, $currencyToEuroConverter, new LocationService());

        $transaction = Transaction::of($binNumber, $amount, $currency);

        $binResponse = new Response(200, body: $binResponseBody);
        $client
            ->expects('get')
            ->with(
                sprintf('%s%s', Binlist::BASE_URL, $binNumber),
                [
                    'headers' => [],
                    'query' => []
                ]
            )
            ->andReturn($binResponse);

        $rateResponse = new Response(200, body: $rateResponseBody);
        $client
            ->expects('get')
            ->with(
                sprintf('%s%s', Exchangerates::BASE_URL, Exchangerates::LATEST_RATES_URI),
                [
                    'headers' => [],
                    'query' => []
                ]
            )
            ->andReturn($rateResponse);

        $actual = $sut->calculate($transaction);

        $expected = ceil((($amount / $usdRate) * CommissionFee::NON_EURO_COUNTRIES_COMMISSION_FEE) * 100) / 100;

        self::assertEquals($expected, $actual);
    }
}
