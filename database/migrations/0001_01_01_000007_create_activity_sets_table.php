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
        Schema::create('activity_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_activity_id')->constrained()->cascadeOnDelete();
            $table->integer('set_number');
            $table->integer('reps')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->decimal('distance', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['workout_activity_id', 'set_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_sets');
    }
};
