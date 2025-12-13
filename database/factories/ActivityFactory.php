<?php

namespace Database\Factories;

use App\Enums\EquipmentType;
use App\Models\ActivityType;
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
            'activity_type_id' => null,
            'equipment_type' => fake()->optional()->randomElement(EquipmentType::cases()),
            'muscle_group' => fake()->optional()->randomElement(['Chest', 'Back', 'Legs', 'Arms', 'Shoulders', 'Core']),
            'description' => fake()->optional()->sentence(),
            'instructions' => fake()->optional()->paragraph(),
        ];
    }

    public function withType(ActivityType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type_id' => $type->id,
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
}
