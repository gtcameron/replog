<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * SQLite doesn't support ALTER COLUMN to change nullability,
     * so we recreate the table with the correct schema.
     */
    public function up(): void
    {
        // SQLite requires table recreation to modify column nullability
        DB::statement('PRAGMA foreign_keys=off;');

        // Create new table with nullable email and password
        DB::statement('
            CREATE TABLE users_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NULL UNIQUE,
                email_verified_at DATETIME NULL,
                password VARCHAR(255) NULL,
                remember_token VARCHAR(100) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                two_factor_secret TEXT NULL,
                two_factor_recovery_codes TEXT NULL,
                two_factor_confirmed_at DATETIME NULL,
                family_id INTEGER NULL REFERENCES families(id) ON DELETE SET NULL,
                can_login TINYINT(1) NOT NULL DEFAULT 0
            )
        ');

        // Copy data from old table
        DB::statement('
            INSERT INTO users_new (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, family_id, can_login)
            SELECT id, name, email, email_verified_at, password, remember_token, created_at, updated_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, family_id, can_login
            FROM users
        ');

        // Drop old table
        DB::statement('DROP TABLE users');

        // Rename new table
        DB::statement('ALTER TABLE users_new RENAME TO users');

        DB::statement('PRAGMA foreign_keys=on;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration makes columns nullable - reversing would require
        // removing null values first, which could cause data loss
    }
};
