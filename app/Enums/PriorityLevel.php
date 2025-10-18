<?php

namespace App\Enums;

class PriorityLevel
{
    const LEVEL_1 = 1;
    const LEVEL_2 = 2;
    const LEVEL_3 = 3;
    const LEVEL_4 = 4;
    const LEVEL_5 = 5;

    public static function all(): array
    {
        return [
            self::LEVEL_1,
            self::LEVEL_2,
            self::LEVEL_3,
            self::LEVEL_4,
            self::LEVEL_5,
        ];
    }

    public static function getMultiplier(int $level): float
    {
        return match($level) {
            self::LEVEL_1 => 1.0,
            self::LEVEL_2 => 2.0,
            self::LEVEL_3 => 3.0,
            self::LEVEL_4 => 4.0,
            self::LEVEL_5 => 5.0,
            default => 1.0,
        };
    }

    public static function isValid(int $level): bool
    {
        return in_array($level, self::all());
    }

    public static function labels(): array
    {
        return [
            self::LEVEL_1 => 'Priority 1 (Lowest)',
            self::LEVEL_2 => 'Priority 2',
            self::LEVEL_3 => 'Priority 3',
            self::LEVEL_4 => 'Priority 4',
            self::LEVEL_5 => 'Priority 5 (Highest)',
        ];
    }
}

