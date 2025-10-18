<?php

namespace App\Enums;

class BatchStatus
{
    const PENDING = 'pending';
    const READY = 'ready';
    const NOTIFIED = 'notified';
    const PROCESSED = 'processed';

    public static function all(): array
    {
        return [
            self::PENDING,
            self::READY,
            self::NOTIFIED,
            self::PROCESSED,
        ];
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, self::all());
    }
}

