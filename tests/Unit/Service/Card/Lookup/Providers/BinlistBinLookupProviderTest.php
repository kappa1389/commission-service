<?php

namespace Test\Unit\Service\Card\Lookup\Providers;


use App\Constants\Binlist;
use App\Exceptions\InvalidBinException;
use App\Exceptions\RemoteServerException;
use App\Service\Card\Lookup\Providers\BinlistProvider\BinlistBinLookupProvider;
use App\Service\Http\HttpClient;
use App\Service\Http\HttpStatus;
use App\Service\Http\Response;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Test\Unit\BaseUnitTestCase;

class BinlistBinLookupProviderTest extends BaseUnitTestCase
{
    private LegacyMockInterface|MockInterface|HttpClient $client;
    private BinlistBinLookupProvider $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(HttpClient::class);

        $this->sut = new BinlistBinLookupProvider($this->client);
    }

    /**
     * @dataProvider failureStatusProvider
     */
    public function testShouldThrowExceptionIfRemoteServerRespondsWithError(int $status, string $exceptionClass)
    {
        $binNumber = 1234;
        $errorMessage = 'dummy message';

        $response = Mockery::mock(Response::class);
        $response->expects('isSuccessful')->withNoArgs()->andReturnFalse();
        $response->expects('status')->withNoArgs()->andReturn($status);
        $response->expects('message')->withNoArgs()->andReturn($errorMessage);

        $this->client
            ->expects('get')
            ->with(sprintf('%s%s', Binlist::BASE_URL, $binNumber))
            ->andReturn($response);

        self::expectException($exceptionClass);
        self::expectErrorMessage($errorMessage);

        $this->sut->lookup($binNumber);
    }

    public function testShouldReturnBinIfRemoteServerRespondsWithSuccess()
    {
        $binNumber = 1234;
        $countryCode = 'dummy-code';
        $body = sprintf('{"country":{"alpha2":"%s"}}', $countryCode);

        $response = Mockery::mock(Response::class);
        $response->expects('isSuccessful')->withNoArgs()->andReturnTrue();
        $response->expects('body')->withNoArgs()->andReturn($body);

        $this->client
            ->expects('get')
            ->with(sprintf('%s%s', Binlist::BASE_URL, $binNumber))
            ->andReturn($response);

        $bin = $this->sut->lookup($binNumber);

        self::assertEquals($countryCode, $bin->getCountryCode());
    }

    public function failureStatusProvider(): array
    {
        return [
            ['status' => HttpStatus::BAD_REQUEST, InvalidBinException::class],
            ['status' => HttpStatus::NOT_FOUND, InvalidBinException::class],
            ['status' => HttpStatus::SERVER_ERROR, RemoteServerException::class],
        ];
    }
}