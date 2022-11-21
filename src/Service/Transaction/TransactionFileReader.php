<?php

namespace App\Service\Transaction;

use App\DTO\Transaction;

class TransactionFileReader
{
    /**
     * @param  string $filePath
     * @return array<Transaction>
     */
    public function read(string $filePath): array
    {
        $file = file_get_contents($filePath);
        $rows = explode("\n", $file);

        $transactions = [];
        foreach ($rows as $row) {
            if (empty($row)) { break;
            }

            $row = json_decode($row, true);
            $transactions[] = Transaction::of(
                $row['bin'],
                $row['amount'],
                $row['currency']
            );
        }

        return $transactions;
    }
}
