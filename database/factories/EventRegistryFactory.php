<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JuniorFontenele\LaravelEvents\Models\EventRegistry;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\JuniorFontenele\LaravelEvents\Models\EventRegistry>
 */
class EventRegistryFactory extends Factory
{
    protected $model = EventRegistry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'className' => fake()->word() . 'Event',
            'type' => fake()->randomElement(['user', 'system']),
            'level' => fake()->randomElement(['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug']),
            'description' => fake()->sentence(6),
        ];
    }
}
