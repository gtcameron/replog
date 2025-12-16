<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { AppPageProps } from '@/types';
import { show, end } from '@/actions/App/Http/Controllers/WorkoutController';

const page = usePage<AppPageProps>();
const activeWorkout = computed(() => page.props?.activeWorkout ?? null);

const elapsedSeconds = ref(0);
let timerInterval: ReturnType<typeof setInterval> | null = null;

function updateTimer() {
    if (activeWorkout.value) {
        const startTime = new Date(activeWorkout.value.started_at).getTime();
        elapsedSeconds.value = Math.floor((Date.now() - startTime) / 1000);
    }
}

function formatTime(seconds: number): string {
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    if (hrs > 0) {
        return `${hrs}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

onMounted(() => {
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

function endWorkout() {
    if (activeWorkout.value) {
        router.post(end.url({ workout: activeWorkout.value.id }));
    }
}
</script>

<template>
    <div
        v-if="activeWorkout"
        class="fixed top-0 left-0 right-0 z-50 bg-primary text-primary-foreground py-2 px-4 shadow-md"
    >
        <div class="mx-auto max-w-7xl flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="font-mono text-xl font-bold tabular-nums">{{ formatTime(elapsedSeconds) }}</span>
                <span class="text-sm opacity-90">
                    {{ activeWorkout.user?.name }}'s Workout
                    <span v-if="activeWorkout.name" class="text-primary-foreground/70">
                        - {{ activeWorkout.name }}
                    </span>
                </span>
            </div>
            <div class="flex items-center gap-2">
                <Link :href="show.url({ workout: activeWorkout.id })">
                    <Button variant="secondary" size="sm">View Workout</Button>
                </Link>
                <Button variant="destructive" size="sm" @click="endWorkout">
                    End Workout
                </Button>
            </div>
        </div>
    </div>
</template>
