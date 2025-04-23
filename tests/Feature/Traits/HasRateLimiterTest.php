<?php

declare(strict_types = 1);

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\RateLimiter;
use JuniorFontenele\LaravelEvents\Tests\TestCase;

class HasRateLimiterTest extends TestCase
{
    use \JuniorFontenele\LaravelEvents\Traits\HasRateLimiter;

    public function testRateLimiterEnabled(): void
    {
        config(['events.rate-limit.enabled' => true]);
        $this->assertTrue($this->isRateLimiterEnabled());

        config(['events.rate-limit.enabled' => false]);
        $this->assertFalse($this->isRateLimiterEnabled());
    }

    public function testTooManyAttempts(): void
    {
        config(['events.rate-limit.enabled' => true]);
        config(['events.rate-limit.max_events' => 2]);

        $eventName = 'test-event';
        RateLimiter::clear($this->getRateLimiterKey($eventName));

        $this->hitRateLimiter($eventName);
        $this->hitRateLimiter($eventName);

        $this->assertTrue($this->tooManyAttempts($eventName));
    }

    public function testResetRateLimiter(): void
    {
        config(['events.rate-limit.enabled' => true]);

        $eventName = 'test-event';
        $this->hitRateLimiter($eventName);
        $this->resetRateLimiter($eventName);

        $this->assertEquals(0, $this->getRateLimitAttempts($eventName));
    }
}
