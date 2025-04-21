<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JuniorFontenele\LaravelEvents\Enums\EventLevel;
use JuniorFontenele\LaravelEvents\Enums\EventType;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property EventType $type
 * @property EventLevel $level
 * @property string $className
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 */
class EventRegistry extends Model
{
    protected $table = 'event_registry';

    protected $fillable = [
        'id',
        'name',
        'description',
        'type',
        'level',
        'className',
        'shouldLog',
        'shouldWriteToDatabase',
    ];

    protected static function booted()
    {
        static::creating(function (EventRegistry $eventRegistry) {
            if (! $eventRegistry->id) {
                $eventRegistry->id = $eventRegistry->max('id') + 1;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'type' => EventType::class,
            'level' => EventLevel::class,
            'shouldLog' => 'boolean',
            'shouldWriteToDatabase' => 'boolean',
        ];
    }

    /** @return HasMany<Event, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_id');
    }
}
