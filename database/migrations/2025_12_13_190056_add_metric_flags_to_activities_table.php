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
        Schema::table('activities', function (Blueprint $table) {
            $table->boolean('tracks_sets')->default(false)->after('instructions');
            $table->boolean('tracks_reps')->default(false)->after('tracks_sets');
            $table->boolean('tracks_weight')->default(false)->after('tracks_reps');
            $table->boolean('tracks_duration')->default(false)->after('tracks_weight');
            $table->boolean('tracks_distance')->default(false)->after('tracks_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'tracks_sets',
                'tracks_reps',
                'tracks_weight',
                'tracks_duration',
                'tracks_distance',
            ]);
        });
    }
};
