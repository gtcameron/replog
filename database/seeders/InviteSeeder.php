<?php

namespace Database\Seeders;

use App\Models\Invite;
use Illuminate\Database\Seeder;

class InviteSeeder extends Seeder
{
    /**
     * Seed the invites table with allowed emails.
     */
    public function run(): void
    {
        $emails = [
            'caubuchon@gmail.com',
        ];

        foreach ($emails as $email) {
            Invite::firstOrCreate(['email' => strtolower($email)]);
        }

        $this->command->info('Invites seeded: '.implode(', ', $emails));
    }
}
