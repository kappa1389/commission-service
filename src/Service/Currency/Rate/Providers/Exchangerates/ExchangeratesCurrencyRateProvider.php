<?php

namespace App\Service\Currency\Rate\Providers\Exchangerates;

use App\Constants\Exchangerates;
use App\DTO\Currency;
use App\Exceptions\CurrencyRateRequestException;
use App\Exceptions\RemoteServerException;
use App\Service\Currency\Rate\CurrencyRateProviderInterface;
use App\Service\Http\HttpClient;
use App\Service\Http\Response;

class ExchangeratesCurrencyRateProvider implements CurrencyRateProviderInterface
{
    public function __construct(private HttpClient $client)
    {
    }

    /**
     * @throws CurrencyRateRequestException
     * @throws RemoteServerException
     */
    public function calculateRate(Currency $to, ?Currency $from = null): float
    {
        $response = $this->client->get(
            sprintf('%s%s', Exchangerates::BASE_URL, Exchangerates::LATEST_RATES_URI),
            query: $this->prepareQuery($from)
        );

        if (!$response->isSuccessful()) {
            throw new RemoteServerException($response->message());
        }

        if ($error = $this->hasError($response)) {
            throw new CurrencyRateRequestException($error);
        }

        $body = json_decode($response->body(), true);

        return $body['rates'][$to->getCode()] ?? 0;
    }

    private function hasError(Response $response): false|string
    {
        $body = json_decode($response->body(), true);

        if (!$body['success']) {
            return $body['error']['info'];
        }

        return false;
    }

    private function prepareQuery(?Currency $from): array
    {
        $query = [];

        $accessKey = $this->getAccessKey();
        if ($accessKey !== null) {
            $query = ['access_key' => $accessKey];
        }

        if ($from !== null) {
            $query = array_merge($query, ['base' => $from->getCode()]);
        }

        return $query;
    }

    public function getAccessKey(): ?string
    {
        return Exchangerates::ACCESS_KEY;
    }
}
