<?php

namespace App\Service\Card\Lookup;

use App\DTO\Bin;
use App\Exceptions\InvalidBinException;
use App\Exceptions\RemoteServerException;

interface BinLookupProviderInterface
{
    /**
     * @throws InvalidBinException
     * @throws RemoteServerException
     */
    public function lookup(int $binNumber): Bin;
}
