<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Service\Card\Lookup\Providers\BinlistProvider\BinlistBinLookupProvider;
use App\Service\Commission\CommissionCalculator;
use App\Service\Currency\Converter\CurrencyToEuroConverter;
use App\Service\Currency\Rate\Providers\Exchangerates\ExchangeratesCurrencyRateProvider;
use App\Service\Http\HttpClient;
use App\Service\Location\LocationService;
use App\Service\Transaction\TransactionFileReader;
use GuzzleHttp\Client;


$binLookupProvider = new BinlistBinLookupProvider(new HttpClient(new Client()));
$currencyToEuroConverter = new CurrencyToEuroConverter(new ExchangeratesCurrencyRateProvider(new HttpClient(new Client())));
$commissionCalculator = new CommissionCalculator($binLookupProvider, $currencyToEuroConverter, new LocationService());

$transactions = (new TransactionFileReader())->read($argv[1]);

foreach ($transactions as $transaction) {
    echo $commissionCalculator->calculate($transaction);
    print "\n";
}

