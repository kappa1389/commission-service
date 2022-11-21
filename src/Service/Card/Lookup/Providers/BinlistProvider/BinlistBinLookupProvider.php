<?php

namespace App\Service\Card\Lookup\Providers\BinlistProvider;

use App\Constants\Binlist;
use App\DTO\Bin;
use App\DTO\Country;
use App\Exceptions\InvalidBinException;
use App\Exceptions\RemoteServerException;
use App\Service\Card\Lookup\BinLookupProviderInterface;
use App\Service\Http\HttpClient;
use App\Service\Http\HttpStatus;
use App\Service\Http\Response;

class BinlistBinLookupProvider implements BinLookupProviderInterface
{

    public function __construct(private HttpClient $client)
    {
    }

    /**
     * @throws InvalidBinException
     * @throws RemoteServerException
     */
    public function lookup(int $binNumber): Bin
    {
        $response = $this->client->get(
            sprintf('%s%s', Binlist::BASE_URL, $binNumber)
        );

        if (!$response->isSuccessful()) {
            $this->throwException($response);
        }

        $body = json_decode($response->body(), true);

        return new Bin(
            new Country($body['country']['alpha2'])
        );
    }

    /**
     * @throws InvalidBinException
     * @throws RemoteServerException
     */
    private function throwException(Response $response): void
    {
        if (in_array($response->status(), [HttpStatus::BAD_REQUEST, HttpStatus::NOT_FOUND])) {
            throw new InvalidBinException($response->message());
        }

        throw new RemoteServerException($response->message());
    }
}
