<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityType>
 */
class ActivityTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->optional()->sentence(),
            'color' => fake()->hexColor(),
            'icon' => null,
        ];
    }

    public function exercise(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Exercise',
            'description' => 'Gym and fitness exercises',
            'color' => '#ef4444',
        ]);
    }

    public function drill(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Drill',
            'description' => 'Sports drills and practice activities',
            'color' => '#3b82f6',
        ]);
    }

    public function habit(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Habit',
            'description' => 'Daily habits and routines',
            'color' => '#22c55e',
        ]);
    }
}
