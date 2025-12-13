<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Family;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $family = Family::first();

        if (! $family) {
            $this->command->error('No family found. Run php artisan app:create-first-family first.');

            return;
        }

        $activities = [
            [
                'name' => 'Bench Press',
                'activity_type_id' => 2, // Free Weight
                'muscle_group' => 'Chest',
                'description' => 'Barbell bench press for chest development',
                'tracks_sets' => true,
                'tracks_reps' => true,
                'tracks_weight' => true,
            ],
            [
                'name' => 'Squats',
                'activity_type_id' => 2, // Free Weight
                'muscle_group' => 'Legs',
                'description' => 'Barbell back squats for leg strength',
                'tracks_sets' => true,
                'tracks_reps' => true,
                'tracks_weight' => true,
            ],
            [
                'name' => 'Pull-ups',
                'activity_type_id' => 3, // Body Weight
                'muscle_group' => 'Back',
                'description' => 'Bodyweight pull-ups for back and biceps',
                'tracks_sets' => true,
                'tracks_reps' => true,
            ],
            [
                'name' => 'Leg Press',
                'activity_type_id' => 1, // Machine
                'muscle_group' => 'Legs',
                'description' => 'Machine leg press for quadriceps',
                'tracks_sets' => true,
                'tracks_reps' => true,
                'tracks_weight' => true,
            ],
            [
                'name' => 'Basketball',
                'activity_type_id' => 4, // Sports
                'muscle_group' => 'Full Body',
                'description' => 'Basketball game or practice',
                'tracks_duration' => true,
            ],
        ];

        foreach ($activities as $activity) {
            Activity::updateOrCreate(
                ['name' => $activity['name'], 'family_id' => $family->id],
                array_merge($activity, ['family_id' => $family->id])
            );
        }

        $this->command->info('Created 5 test activities for family: '.$family->name);
    }
}
