<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('can_login')->default(false)->after('email');
        });

        // For existing users, set can_login to true (they have email/password)
        DB::table('users')->whereNotNull('email')->whereNotNull('password')->update(['can_login' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
            $table->dropColumn('can_login');
        });
    }
};
