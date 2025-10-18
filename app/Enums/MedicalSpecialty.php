<?php

namespace App\Enums;

class MedicalSpecialty
{
    const CARDIOLOGY = 'cardiology';
    const ORTHOPEDICS = 'orthopedics';
    const NEUROLOGY = 'neurology';
    const PEDIATRICS = 'pediatrics';
    const ONCOLOGY = 'oncology';
    const DERMATOLOGY = 'dermatology';
    const RADIOLOGY = 'radiology';
    const PSYCHIATRY = 'psychiatry';
    const GASTROENTEROLOGY = 'gastroenterology';
    const ENDOCRINOLOGY = 'endocrinology';

    public static function all(): array
    {
        return [
            self::CARDIOLOGY,
            self::ORTHOPEDICS,
            self::NEUROLOGY,
            self::PEDIATRICS,
            self::ONCOLOGY,
            self::DERMATOLOGY,
            self::RADIOLOGY,
            self::PSYCHIATRY,
            self::GASTROENTEROLOGY,
            self::ENDOCRINOLOGY,
        ];
    }

    public static function labels(): array
    {
        return [
            self::CARDIOLOGY => 'Cardiology',
            self::ORTHOPEDICS => 'Orthopedics',
            self::NEUROLOGY => 'Neurology',
            self::PEDIATRICS => 'Pediatrics',
            self::ONCOLOGY => 'Oncology',
            self::DERMATOLOGY => 'Dermatology',
            self::RADIOLOGY => 'Radiology',
            self::PSYCHIATRY => 'Psychiatry',
            self::GASTROENTEROLOGY => 'Gastroenterology',
            self::ENDOCRINOLOGY => 'Endocrinology',
        ];
    }

    public static function isValid(string $specialty): bool
    {
        return in_array($specialty, self::all());
    }
}

