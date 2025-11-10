<?php

namespace App\Enums;

enum PriorityLevel: int
{
    case LEVEL_1 = 1;
    case LEVEL_2 = 2;
    case LEVEL_3 = 3;
    case LEVEL_4 = 4;
    case LEVEL_5 = 5;

    public function label(): string
    {
        return match ($this) {
            self::LEVEL_1 => 'Level 1 - Routine',
            self::LEVEL_2 => 'Level 2 - Low',
            self::LEVEL_3 => 'Level 3 - Normal',
            self::LEVEL_4 => 'Level 4 - High',
            self::LEVEL_5 => 'Level 5 - Urgent',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
