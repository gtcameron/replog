<?php

namespace Database\Factories;

use App\Enums\EquipmentType;
use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'family_id' => Family::factory(),
            'equipment_type' => fake()->optional()->randomElement(EquipmentType::cases()),
            'muscle_group' => fake()->optional()->randomElement(['Chest', 'Back', 'Legs', 'Arms', 'Shoulders', 'Core']),
            'description' => fake()->optional()->sentence(),
            'instructions' => fake()->optional()->paragraph(),
            'tracks_reps' => true,
            'tracks_weight' => true,
            'tracks_duration' => false,
            'tracks_distance' => false,
        ];
    }

    public function forFamily(Family $family): static
    {
        return $this->state(fn (array $attributes) => [
            'family_id' => $family->id,
        ]);
    }

    public function barbell(): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_type' => EquipmentType::Barbell,
        ]);
    }

    public function bodyweight(): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_type' => EquipmentType::Bodyweight,
        ]);
    }

    public function cardio(): static
    {
        return $this->state(fn (array $attributes) => [
            'tracks_reps' => false,
            'tracks_weight' => false,
            'tracks_duration' => true,
            'tracks_distance' => true,
        ]);
    }
}
