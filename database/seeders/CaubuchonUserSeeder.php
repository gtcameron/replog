<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaubuchonUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $existingUser = User::where('email', 'caubuchon@gmail.com')->first();

        if ($existingUser) {
            if (! $existingUser->is_admin) {
                $existingUser->update(['is_admin' => true]);
                $this->command->info('User caubuchon@gmail.com updated to admin.');
            } else {
                $this->command->info('User caubuchon@gmail.com already exists as admin, skipping.');
            }

            return;
        }

        $family = Family::firstOrCreate(['name' => 'Caubuchon']);

        $password = Str::random(16);

        User::factory()->forFamily($family)->create([
            'name' => 'Caubuchon',
            'email' => 'caubuchon@gmail.com',
            'password' => $password,
            'is_admin' => true,
        ]);

        $this->command->info("Created admin user caubuchon@gmail.com with password: {$password}");
    }
}
