<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Traits;

use Illuminate\Support\Facades\RateLimiter;

trait HasRateLimiter
{
    protected function isRateLimiterEnabled(): bool
    {
        return config('events.rate-limit.enabled');
    }

    protected function getRateLimiterKey(string $eventName): string
    {
        return config('events.rate-limit.key') . ':' . $eventName;
    }

    protected function getMaxEvents(): int
    {
        return config('events.rate-limit.max_events');
    }

    protected function getDecaySeconds(): int
    {
        return config('events.rate-limit.decay_seconds');
    }

    protected function tooManyAttempts(string $eventName): bool
    {
        return $this->isRateLimiterEnabled() && RateLimiter::tooManyAttempts($this->getRateLimiterKey($eventName), $this->getMaxEvents());
    }

    protected function hitRateLimiter(string $eventName): void
    {
        if ($this->isRateLimiterEnabled()) {
            RateLimiter::hit($this->getRateLimiterKey($eventName), $this->getDecaySeconds());
        }
    }

    protected function resetRateLimiter(string $eventName): void
    {
        if ($this->isRateLimiterEnabled()) {
            RateLimiter::clear($this->getRateLimiterKey($eventName));
        }
    }

    protected function getRateLimitAttempts(string $eventName): int
    {
        return RateLimiter::attempts($this->getRateLimiterKey($eventName));
    }
}
