<?php

namespace App\Enums;

class ClaimStatus
{
    const PENDING = 'pending';
    const BATCHED = 'batched';
    const PROCESSED = 'processed';

    public static function all(): array
    {
        return [
            self::PENDING,
            self::BATCHED,
            self::PROCESSED,
        ];
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, self::all());
    }
}

