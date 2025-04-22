<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Event extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'name',
        'event_id',
        'event_name',
        'event_class',
        'level',
        'type',
        'description',
        'context',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'trace_id',
        'request_id',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'context' => 'encrypted:array',
        ];
    }

    public function getTable(): string
    {
        return config('events.table_prefix', 'laravel_events_') . 'events';
    }

    /** @return MorphTo<Model, $this> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return MorphTo<Model, $this> */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return BelongsTo<EventRegistry, $this> */
    public function eventRegistry(): BelongsTo
    {
        return $this->belongsTo(EventRegistry::class);
    }
}
