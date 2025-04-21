<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Enums;

enum EventType: string
{
    case USER = 'user';
    case SYSTEM = 'system';

    public function getName(): string
    {
        return match ($this) {
            self::USER => 'User',
            self::SYSTEM => 'System',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[$case->value] = $case->getName();
        }

        return $values;
    }
}
