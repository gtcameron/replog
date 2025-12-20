<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workout_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('logged_by_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('workout_id')->nullable()->constrained()->nullOnDelete();
            $table->date('performed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'performed_at']);
            $table->index(['user_id', 'performed_at']);
            $table->index('workout_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_activities');
    }
};
