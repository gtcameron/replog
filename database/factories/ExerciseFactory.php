<?php

namespace Database\Factories;

use App\Enums\EquipmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $exercises = [
            ['name' => 'Bench Press', 'equipment' => EquipmentType::Barbell, 'muscle' => 'Chest'],
            ['name' => 'Squat', 'equipment' => EquipmentType::Barbell, 'muscle' => 'Legs'],
            ['name' => 'Deadlift', 'equipment' => EquipmentType::Barbell, 'muscle' => 'Back'],
            ['name' => 'Bicep Curl', 'equipment' => EquipmentType::Dumbbell, 'muscle' => 'Arms'],
            ['name' => 'Lat Pulldown', 'equipment' => EquipmentType::Cable, 'muscle' => 'Back'],
            ['name' => 'Leg Press', 'equipment' => EquipmentType::Machine, 'muscle' => 'Legs'],
            ['name' => 'Pull Up', 'equipment' => EquipmentType::Bodyweight, 'muscle' => 'Back'],
            ['name' => 'Push Up', 'equipment' => EquipmentType::Bodyweight, 'muscle' => 'Chest'],
        ];

        $exercise = fake()->randomElement($exercises);

        return [
            'name' => $exercise['name'],
            'equipment_type' => $exercise['equipment'],
            'muscle_group' => $exercise['muscle'],
            'description' => fake()->optional()->sentence(),
            'instructions' => fake()->optional()->paragraph(),
        ];
    }

    public function barbell(): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_type' => EquipmentType::Barbell,
        ]);
    }

    public function dumbbell(): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_type' => EquipmentType::Dumbbell,
        ]);
    }

    public function machine(): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_type' => EquipmentType::Machine,
        ]);
    }

    public function bodyweight(): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_type' => EquipmentType::Bodyweight,
        ]);
    }
}
