<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Listeners;

use Illuminate\Support\Facades\Log;
use JuniorFontenele\LaravelEvents\BaseEvent;
use JuniorFontenele\LaravelEvents\Traits\HasRateLimiter;

class WriteEventsToLog
{
    use HasRateLimiter;

    /**
     * Handle the event.
     * @param string $eventName
     * @param array<int, mixed> $eventData
     */
    public function handle(string $eventName, array $eventData): void
    {
        $event = $eventData[0];

        if (! $event instanceof BaseEvent) {
            return;
        }

        if (! $event->shouldLog()) {
            return;
        }

        if ($this->tooManyAttempts($eventName)) {
            return;
        }

        $context = $event->getFullContext();
        $level = mb_strtolower($event->getLogLevel());
        $message = $event->getName();

        Log::{$level}($message, $context);

        $this->hitRateLimiter($eventName);
    }
}
