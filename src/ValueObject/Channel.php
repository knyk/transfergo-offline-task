<?php

declare(strict_types=1);

namespace App\ValueObject;

enum Channel: string
{
    case Email = 'email';
    case SMS = 'sms';
    case Push = 'push';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn(self $enum) => $enum->value, self::cases());
    }
}
