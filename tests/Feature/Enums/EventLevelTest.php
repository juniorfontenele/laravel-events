<?php

declare(strict_types = 1);

namespace Tests\Feature\Enums;

use JuniorFontenele\LaravelEvents\Enums\EventLevel;
use JuniorFontenele\LaravelEvents\Tests\TestCase;

class EventLevelTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('Emergency', EventLevel::EMERGENCY->getName());
        $this->assertEquals('Debug', EventLevel::DEBUG->getName());
    }

    public function testToArray(): void
    {
        $expected = [
            'emergency' => 'Emergency',
            'alert' => 'Alert',
            'critical' => 'Critical',
            'error' => 'Error',
            'warning' => 'Warning',
            'notice' => 'Notice',
            'info' => 'Info',
            'debug' => 'Debug',
        ];

        $this->assertEquals($expected, EventLevel::toArray());
    }
}
