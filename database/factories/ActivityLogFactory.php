<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'family_id' => Family::factory(),
            'activity_id' => Activity::factory(),
            'user_id' => User::factory(),
            'logged_by_id' => fn (array $attributes) => $attributes['user_id'],
            'performed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'sets' => fake()->optional()->numberBetween(1, 10),
            'reps' => fake()->optional()->numberBetween(1, 20),
            'weight' => fake()->optional()->randomFloat(2, 5, 500),
            'duration_seconds' => fake()->optional()->numberBetween(30, 7200),
            'distance' => fake()->optional()->randomFloat(2, 0.1, 50),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forFamily(Family $family): static
    {
        return $this->state(fn (array $attributes) => [
            'family_id' => $family->id,
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'family_id' => $user->family_id,
        ]);
    }

    public function loggedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'logged_by_id' => $user->id,
        ]);
    }

    public function withStrengthData(): static
    {
        return $this->state(fn (array $attributes) => [
            'sets' => fake()->numberBetween(3, 5),
            'reps' => fake()->numberBetween(6, 12),
            'weight' => fake()->randomFloat(2, 20, 300),
            'duration_seconds' => null,
            'distance' => null,
        ]);
    }

    public function withCardioData(): static
    {
        return $this->state(fn (array $attributes) => [
            'sets' => null,
            'reps' => null,
            'weight' => null,
            'duration_seconds' => fake()->numberBetween(600, 3600),
            'distance' => fake()->randomFloat(2, 1, 20),
        ]);
    }
}
