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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('equipment_type')->nullable();
            $table->string('muscle_group')->nullable();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('tracks_reps')->default(false);
            $table->boolean('tracks_weight')->default(false);
            $table->boolean('tracks_duration')->default(false);
            $table->boolean('tracks_distance')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
