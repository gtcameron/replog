<?php

namespace Database\Seeders;

use App\Enums\EquipmentType;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // Barbell exercises
            ['name' => 'Bench Press', 'equipment_type' => EquipmentType::Barbell, 'muscle_group' => 'Chest', 'description' => 'Classic chest exercise using a barbell'],
            ['name' => 'Squat', 'equipment_type' => EquipmentType::Barbell, 'muscle_group' => 'Legs', 'description' => 'Compound lower body exercise'],
            ['name' => 'Deadlift', 'equipment_type' => EquipmentType::Barbell, 'muscle_group' => 'Back', 'description' => 'Full body pulling movement'],
            ['name' => 'Overhead Press', 'equipment_type' => EquipmentType::Barbell, 'muscle_group' => 'Shoulders', 'description' => 'Standing shoulder press'],
            ['name' => 'Barbell Row', 'equipment_type' => EquipmentType::Barbell, 'muscle_group' => 'Back', 'description' => 'Bent over rowing movement'],

            // Dumbbell exercises
            ['name' => 'Dumbbell Curl', 'equipment_type' => EquipmentType::Dumbbell, 'muscle_group' => 'Arms', 'description' => 'Bicep isolation exercise'],
            ['name' => 'Dumbbell Lateral Raise', 'equipment_type' => EquipmentType::Dumbbell, 'muscle_group' => 'Shoulders', 'description' => 'Side deltoid isolation'],
            ['name' => 'Dumbbell Bench Press', 'equipment_type' => EquipmentType::Dumbbell, 'muscle_group' => 'Chest', 'description' => 'Chest press with dumbbells'],
            ['name' => 'Dumbbell Lunge', 'equipment_type' => EquipmentType::Dumbbell, 'muscle_group' => 'Legs', 'description' => 'Unilateral leg exercise'],

            // Machine exercises
            ['name' => 'Leg Press', 'equipment_type' => EquipmentType::Machine, 'muscle_group' => 'Legs', 'description' => 'Machine-based leg exercise'],
            ['name' => 'Chest Press Machine', 'equipment_type' => EquipmentType::Machine, 'muscle_group' => 'Chest', 'description' => 'Guided chest press movement'],
            ['name' => 'Leg Curl', 'equipment_type' => EquipmentType::Machine, 'muscle_group' => 'Legs', 'description' => 'Hamstring isolation'],
            ['name' => 'Leg Extension', 'equipment_type' => EquipmentType::Machine, 'muscle_group' => 'Legs', 'description' => 'Quadriceps isolation'],

            // Cable exercises
            ['name' => 'Lat Pulldown', 'equipment_type' => EquipmentType::Cable, 'muscle_group' => 'Back', 'description' => 'Vertical pulling movement'],
            ['name' => 'Cable Fly', 'equipment_type' => EquipmentType::Cable, 'muscle_group' => 'Chest', 'description' => 'Chest isolation with cables'],
            ['name' => 'Tricep Pushdown', 'equipment_type' => EquipmentType::Cable, 'muscle_group' => 'Arms', 'description' => 'Tricep isolation exercise'],
            ['name' => 'Face Pull', 'equipment_type' => EquipmentType::Cable, 'muscle_group' => 'Shoulders', 'description' => 'Rear deltoid and upper back'],

            // Bodyweight exercises
            ['name' => 'Pull Up', 'equipment_type' => EquipmentType::Bodyweight, 'muscle_group' => 'Back', 'description' => 'Vertical pulling with body weight'],
            ['name' => 'Push Up', 'equipment_type' => EquipmentType::Bodyweight, 'muscle_group' => 'Chest', 'description' => 'Classic chest exercise'],
            ['name' => 'Dip', 'equipment_type' => EquipmentType::Bodyweight, 'muscle_group' => 'Chest', 'description' => 'Chest and tricep exercise'],
            ['name' => 'Plank', 'equipment_type' => EquipmentType::Bodyweight, 'muscle_group' => 'Core', 'description' => 'Core stability exercise'],

            // Kettlebell exercises
            ['name' => 'Kettlebell Swing', 'equipment_type' => EquipmentType::Kettlebell, 'muscle_group' => 'Full Body', 'description' => 'Hip hinge explosive movement'],
            ['name' => 'Goblet Squat', 'equipment_type' => EquipmentType::Kettlebell, 'muscle_group' => 'Legs', 'description' => 'Front-loaded squat variation'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}
