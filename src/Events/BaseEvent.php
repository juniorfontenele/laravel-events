<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JuniorFontenele\LaravelEvents\Enums\EventLevel;
use JuniorFontenele\LaravelEvents\Enums\EventType;

abstract class BaseEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $id;

    public string $name;

    public EventType $type;

    public EventLevel $level;

    public string $description = '';

    public bool $shouldLog = true;

    public bool $shouldWriteToDatabase = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name ?? Str::headline(class_basename($this));
    }

    public function getLevel(): EventLevel
    {
        return $this->level;
    }

    public function getLogLevel(): string
    {
        return mb_strtolower($this->level->name);
    }

    public function getType(): EventType
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function shouldLog(): bool
    {
        return $this->shouldLog;
    }

    public function shouldWriteToDatabase(): bool
    {
        return $this->shouldWriteToDatabase;
    }

    /** @return array<string, mixed> */
    public function getBaseContext(): array
    {
        $sharedContext = Log::sharedContext();

        $baseContext = [
            'event' => [
                'id' => $this->id,
                'name' => $this->getName(),
                'class' => get_class($this),
                'type' => $this->getType()->value,
                'level' => $this->getLogLevel(),
                'description' => $this->getDescription(),
            ],
        ];

        return array_merge($sharedContext, $baseContext);
    }

    /** @return array<string, mixed> */
    abstract public function getContext(): array;

    /** @return array<string, mixed> */
    public function getFullContext(): array
    {
        $requestContext = [];

        if (session()->has('trace_id')) {
            $requestContext['trace_id'] = session()->get('trace_id');
        }

        if (session()->has('request_id')) {
            $requestContext['request_id'] = session()->get('request_id');
        }

        $subject = $this->getSubject();
        $causer = $this->getCauser();
        $context = array_merge($requestContext, $this->getBaseContext());

        if (count($this->getContext()) > 0) {
            $context['context'] = $this->getContext();
        }

        if ($subject instanceof Model) {
            $context['subject'] = [
                'id' => $subject->getKey(),
                'type' => get_class($subject),
            ];
        }

        if ($causer instanceof Model) {
            $context['causer'] = [
                'id' => $causer->getKey(),
                'type' => get_class($causer),
            ];
        }

        return $context;
    }

    abstract public function getSubject(): ?Model;

    public function getCauser(): ?Model
    {
        return Auth::check() ? Auth::user() : null;
    }

    public function getRoutingKey(): string
    {
        $appName = Str::snake(mb_strtolower(config('app.name')));
        $moduleName = $this->extractModuleFromNamespace();
        $eventName = Str::snake(class_basename($this));

        return sprintf('%s.%s.%s', $appName, $moduleName, $eventName);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->getRoutingKey()),
        ];
    }

    protected function extractModuleFromNamespace(): string
    {
        $namespace = get_class($this);

        $parts = explode('\\', $namespace);

        $eventsIndex = array_search('Events', $parts);

        $defaultModule = config('events.default_module', 'core');
        $namespaceDepth = config('events.namespace_depth', 1);

        if ($eventsIndex !== false) {
            $moduleSegments = [];

            for ($i = 1; $i <= $namespaceDepth; $i++) {
                if (isset($parts[$eventsIndex + $i])) {
                    $moduleSegments[] = $parts[$eventsIndex + $i];
                }
            }

            if ($moduleSegments !== []) {
                return Str::snake(Str::lower(implode('.', $moduleSegments)));
            }
        }

        return Str::snake(Str::lower($defaultModule));
    }
}
