<?php

namespace Database\Factories;

use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'family_id' => Family::factory(),
            'can_login' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user without login credentials (family member only).
     */
    public function withoutLogin(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'password' => null,
            'can_login' => false,
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user for a specific family.
     */
    public function forFamily(Family $family): static
    {
        return $this->state(fn (array $attributes) => [
            'family_id' => $family->id,
        ]);
    }
}
