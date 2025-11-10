<?php

namespace App\Enums;

enum Specialty: string
{
    case CARDIOLOGY = 'cardiology';
    case ORTHOPEDICS = 'orthopedics';

    public function label(): string
    {
        return match ($this) {
            self::CARDIOLOGY => 'Cardiology',
            self::ORTHOPEDICS => 'Orthopedics',
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
