<?php

namespace Test\Unit\Service\Transaction;

use App\DTO\Transaction;
use App\Service\Transaction\TransactionFileReader;
use Test\Unit\BaseUnitTestCase;

class TransactionFileReaderTest extends BaseUnitTestCase
{
    public function testShouldReadFileCorrectly()
    {
        $sut = new TransactionFileReader();

        $transactions = $sut->read('tests/Unit/Service/Transaction/sample.txt');

        self::assertCount(2, $transactions);

        self::assertInstanceOf(Transaction::class, $transactions[0]);
        self::assertEquals(123, $transactions[0]->getBinNumber());
        self::assertEquals(100, $transactions[0]->getAmount());
        self::assertEquals('EUR', $transactions[0]->getCurrencyCode());

        self::assertInstanceOf(Transaction::class, $transactions[1]);
        self::assertEquals(124, $transactions[1]->getBinNumber());
        self::assertEquals(50, $transactions[1]->getAmount());
        self::assertEquals('USD', $transactions[1]->getCurrencyCode());
    }
}