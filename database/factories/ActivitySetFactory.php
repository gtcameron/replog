<?php

namespace Database\Factories;

use App\Models\ActivitySet;
use App\Models\WorkoutActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivitySet>
 */
class ActivitySetFactory extends Factory
{
    protected $model = ActivitySet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_activity_id' => WorkoutActivity::factory(),
            'set_number' => 1,
            'reps' => fake()->numberBetween(6, 12),
            'weight' => fake()->randomFloat(2, 20, 200),
            'duration_seconds' => null,
            'distance' => null,
            'notes' => null,
        ];
    }

    public function forWorkoutActivity(WorkoutActivity $workoutActivity): static
    {
        return $this->state(fn (array $attributes) => [
            'workout_activity_id' => $workoutActivity->id,
        ]);
    }

    public function setNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'set_number' => $number,
        ]);
    }

    public function strength(): static
    {
        return $this->state(fn (array $attributes) => [
            'reps' => fake()->numberBetween(6, 12),
            'weight' => fake()->randomFloat(2, 20, 300),
            'duration_seconds' => null,
            'distance' => null,
        ]);
    }

    public function cardio(): static
    {
        return $this->state(fn (array $attributes) => [
            'reps' => null,
            'weight' => null,
            'duration_seconds' => fake()->numberBetween(600, 3600),
            'distance' => fake()->randomFloat(2, 1, 20),
        ]);
    }
}
