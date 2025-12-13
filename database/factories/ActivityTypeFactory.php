<?php

namespace Database\Factories;

use App\Models\Family;
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
            'family_id' => Family::factory(),
            'description' => fake()->optional()->sentence(),
            'color' => fake()->hexColor(),
            'icon' => null,
        ];
    }

    public function forFamily(Family $family): static
    {
        return $this->state(fn (array $attributes) => [
            'family_id' => $family->id,
        ]);
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
