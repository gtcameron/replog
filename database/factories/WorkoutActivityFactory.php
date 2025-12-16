<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Family;
use App\Models\User;
use App\Models\WorkoutActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutActivity>
 */
class WorkoutActivityFactory extends Factory
{
    protected $model = WorkoutActivity::class;

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
}
