<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class Bo7OperationLockService
{
    private const LOCK_NAME = 'bo7_dataset_operation';

    public function runExclusive(callable $callback, int $waitSeconds = 0): mixed
    {
        $connection = DB::connection();
        $result = $connection->selectOne('SELECT GET_LOCK(?, ?) AS acquired', [
            self::LOCK_NAME,
            max(0, $waitSeconds),
        ]);

        if ((int) ($result->acquired ?? 0) !== 1) {
            throw new RuntimeException(
                'A BO7 dataset import is already running. Wait for it to finish before launching another one.'
            );
        }

        try {
            return $callback();
        } finally {
            try {
                $connection->selectOne('SELECT RELEASE_LOCK(?) AS released', [self::LOCK_NAME]);
            } catch (Throwable) {
                // Best effort release. The connection closing also releases the advisory lock.
            }
        }
    }
}
