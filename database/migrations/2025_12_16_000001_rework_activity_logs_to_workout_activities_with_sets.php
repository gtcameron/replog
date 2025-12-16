<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration:
     * 1. Creates activity_sets table
     * 2. Migrates existing activity_log data to activity_sets
     * 3. Drops metric columns from activity_logs
     * 4. Renames activity_logs to workout_activities
     * 5. Removes activity_type_id and tracks_sets from activities
     * 6. Drops activity_types table
     */
    public function up(): void
    {
        // Step 1: Create activity_sets table
        Schema::create('activity_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_activity_id')->constrained('activity_logs')->cascadeOnDelete();
            $table->unsignedInteger('set_number');
            $table->integer('reps')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->decimal('distance', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['workout_activity_id', 'set_number']);
        });

        // Step 2: Migrate existing data from activity_logs to activity_sets
        DB::table('activity_logs')->orderBy('id')->chunk(100, function ($logs) {
            foreach ($logs as $log) {
                // Determine how many sets to create
                $setCount = max(1, $log->sets ?? 1);

                // Only create sets if there's actual data
                $hasData = $log->reps || $log->weight || $log->duration_seconds || $log->distance;

                if ($hasData) {
                    for ($i = 1; $i <= $setCount; $i++) {
                        DB::table('activity_sets')->insert([
                            'workout_activity_id' => $log->id,
                            'set_number' => $i,
                            'reps' => $log->reps,
                            'weight' => $log->weight,
                            'duration_seconds' => $log->duration_seconds,
                            'distance' => $log->distance,
                            'notes' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        // Step 3: Drop metric columns from activity_logs
        // SQLite requires recreating the table, so we use a transaction
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['sets', 'reps', 'weight', 'duration_seconds', 'distance']);
        });

        // Step 4: Rename activity_logs to workout_activities
        Schema::rename('activity_logs', 'workout_activities');

        // Step 5: Update the foreign key reference in activity_sets
        // SQLite handles this automatically when renaming tables

        // Step 6: Remove activity_type_id and tracks_sets from activities
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['activity_type_id']);
            $table->dropColumn(['activity_type_id', 'tracks_sets']);
        });

        // Step 7: Drop activity_types table
        Schema::dropIfExists('activity_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Recreate activity_types table
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Step 2: Add back activity_type_id and tracks_sets to activities
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('activity_type_id')->nullable()->after('name')->constrained()->nullOnDelete();
            $table->boolean('tracks_sets')->default(true)->after('instructions');
        });

        // Step 3: Rename workout_activities back to activity_logs
        Schema::rename('workout_activities', 'activity_logs');

        // Step 4: Add back metric columns to activity_logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->integer('sets')->nullable()->after('performed_at');
            $table->integer('reps')->nullable()->after('sets');
            $table->decimal('weight', 8, 2)->nullable()->after('reps');
            $table->integer('duration_seconds')->nullable()->after('weight');
            $table->decimal('distance', 10, 2)->nullable()->after('duration_seconds');
        });

        // Step 5: Migrate data back from activity_sets to activity_logs
        // Get the first set for each workout_activity
        $activityLogs = DB::table('activity_logs')->get();
        foreach ($activityLogs as $log) {
            $firstSet = DB::table('activity_sets')
                ->where('workout_activity_id', $log->id)
                ->orderBy('set_number')
                ->first();

            $setCount = DB::table('activity_sets')
                ->where('workout_activity_id', $log->id)
                ->count();

            if ($firstSet) {
                DB::table('activity_logs')
                    ->where('id', $log->id)
                    ->update([
                        'sets' => $setCount ?: null,
                        'reps' => $firstSet->reps,
                        'weight' => $firstSet->weight,
                        'duration_seconds' => $firstSet->duration_seconds,
                        'distance' => $firstSet->distance,
                    ]);
            }
        }

        // Step 6: Drop activity_sets table
        Schema::dropIfExists('activity_sets');
    }
};
