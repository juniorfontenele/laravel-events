<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Listeners;

use JuniorFontenele\LaravelEvents\BaseEvent;
use JuniorFontenele\LaravelEvents\Models\Event;
use JuniorFontenele\LaravelEvents\Traits\HasRateLimiter;

class WriteEventsToDatabase
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

        if (! $event->shouldWriteToDatabase()) {
            return;
        }

        if ($this->tooManyAttempts($eventName)) {
            return;
        }

        $context = $event->getFullContext();
        $level = mb_strtolower($event->getLogLevel());
        $message = $event->getName();

        $eventModelData = [
            'name' => $message,
            'event_id' => $event->getId(),
            'event_class' => get_class($event),
            'level' => $level,
            'type' => $event->getType()->value,
            'description' => $event->description,
            'context' => $context,
            'subject_id' => $event->getSubject()?->getKey(),
            'subject_type' => is_null($event->getSubject()) ? null : get_class($event->getSubject()),
            'causer_id' => $event->getCauser()?->getKey(),
            'causer_type' => is_null($event->getCauser()) ? null : get_class($event->getCauser()),
            'trace_id' => session()->get('trace_id'),
            'request_id' => session()->get('request_id'),
        ];

        Event::create($eventModelData);

        $this->hitRateLimiter($eventName);
    }
}
