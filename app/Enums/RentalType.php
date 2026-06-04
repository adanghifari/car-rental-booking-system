<?php

namespace App\Enums;

enum RentalType: string
{
    case SELF_DRIVE = 'Self Drive';
    case WITH_DRIVER = 'With Driver';

    /**
     * Get all enum values.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }
}
