<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Enums;

enum EventLevel: string
{
    case EMERGENCY = 'emergency';
    case ALERT = 'alert';
    case CRITICAL = 'critical';
    case ERROR = 'error';
    case WARNING = 'warning';
    case NOTICE = 'notice';
    case INFO = 'info';
    case DEBUG = 'debug';

    public function getName(): string
    {
        return match ($this) {
            self::EMERGENCY => 'Emergency',
            self::ALERT => 'Alert',
            self::CRITICAL => 'Critical',
            self::ERROR => 'Error',
            self::WARNING => 'Warning',
            self::NOTICE => 'Notice',
            self::INFO => 'Info',
            self::DEBUG => 'Debug',
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
