<?php

namespace App\Console\Commands;

use App\Models\Family;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateFirstFamily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-first-family';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the first family and admin user for initial setup';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (Family::exists()) {
            $this->error('A family already exists. This command is for initial setup only.');

            return self::FAILURE;
        }

        $familyName = text(
            label: 'What is your family name?',
            required: true,
        );

        $userName = text(
            label: 'What is your name?',
            required: true,
        );

        $email = text(
            label: 'What is your email?',
            required: true,
            validate: fn (string $value) => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Please enter a valid email address.',
        );

        $userPassword = password(
            label: 'Choose a password',
            required: true,
        );

        $family = Family::create(['name' => $familyName]);

        User::create([
            'name' => $userName,
            'email' => $email,
            'password' => Hash::make($userPassword),
            'family_id' => $family->id,
            'can_login' => true,
        ]);

        $this->info("Family '{$familyName}' created with admin user '{$userName}'");
        $this->info('You can now log in at '.url('/login'));

        return self::SUCCESS;
    }
}
