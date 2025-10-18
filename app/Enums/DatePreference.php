<?php

namespace App\Enums;

class DatePreference
{
    const ENCOUNTER = 'encounter';
    const SUBMISSION = 'submission';

    public static function all(): array
    {
        return [
            self::ENCOUNTER,
            self::SUBMISSION,
        ];
    }

    public static function isValid(string $preference): bool
    {
        return in_array($preference, self::all());
    }
}

