<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import type { Workout, PaginatedData, AppPageProps } from '@/types';

import { create, show, destroy } from '@/actions/App/Http/Controllers/WorkoutController';

defineProps<{
    workouts: PaginatedData<Workout>;
}>();

const page = usePage<AppPageProps>();
const activeWorkout = computed(() => page.props.activeWorkout);

const workoutToDelete = ref<Workout | null>(null);

function confirmDelete(workout: Workout) {
    workoutToDelete.value = workout;
}

function deleteWorkout() {
    if (workoutToDelete.value) {
        router.delete(destroy.url({ workout: workoutToDelete.value.id }));
        workoutToDelete.value = null;
    }
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString(undefined, {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
}

function formatTime(dateString: string) {
    return new Date(dateString).toLocaleTimeString(undefined, {
        hour: 'numeric',
        minute: '2-digit',
    });
}

function formatDuration(startedAt: string, endedAt: string | null): string {
    const start = new Date(startedAt).getTime();
    const end = endedAt ? new Date(endedAt).getTime() : Date.now();
    const seconds = Math.floor((end - start) / 1000);

    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hrs > 0) {
        return `${hrs}h ${mins}m`;
    }
    if (mins > 0) {
        return `${mins}m ${secs}s`;
    }
    return `${secs}s`;
}
</script>

<template>
    <Head title="Workouts" />
    <div class="min-h-screen bg-background" :class="{ 'pt-12': activeWorkout }">
        <nav class="border-b bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <Link href="/dashboard" class="text-lg font-semibold text-foreground">
                            RepLog
                        </Link>
                        <Link href="/dashboard" class="text-sm text-muted-foreground hover:text-foreground">
                            Dashboard
                        </Link>
                        <span class="text-sm font-medium text-foreground">Workouts</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Workouts</CardTitle>
                            <CardDescription>Track your workout sessions</CardDescription>
                        </div>
                        <div>
                            <Link v-if="activeWorkout" :href="show.url({ workout: activeWorkout.id })">
                                <Button>Continue Workout</Button>
                            </Link>
                            <Link v-else :href="create.url()">
                                <Button>Start Workout</Button>
                            </Link>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Date</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Member</TableHead>
                                <TableHead>Duration</TableHead>
                                <TableHead>Activities</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="workout in workouts.data" :key="workout.id">
                                <TableCell class="font-medium">
                                    <div>{{ formatDate(workout.started_at) }}</div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ formatTime(workout.started_at) }}
                                    </div>
                                </TableCell>
                                <TableCell>{{ workout.name || 'Workout' }}</TableCell>
                                <TableCell>{{ workout.user?.name }}</TableCell>
                                <TableCell class="font-mono text-sm">
                                    {{ formatDuration(workout.started_at, workout.ended_at) }}
                                </TableCell>
                                <TableCell>
                                    {{ workout.activity_logs_count || 0 }}
                                </TableCell>
                                <TableCell>
                                    <Badge v-if="!workout.ended_at" variant="default">
                                        Active
                                    </Badge>
                                    <Badge v-else variant="secondary">
                                        Completed
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="show.url({ workout: workout.id })">
                                            <Button variant="outline" size="sm">
                                                {{ workout.ended_at ? 'View' : 'Continue' }}
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="workout.ended_at"
                                            variant="destructive"
                                            size="sm"
                                            @click="confirmDelete(workout)"
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="workouts.data.length === 0">
                                <TableCell colspan="7" class="text-center text-muted-foreground py-8">
                                    No workouts yet. Start your first workout!
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </main>
    </div>

    <AlertDialog :open="workoutToDelete !== null" @update:open="workoutToDelete = null">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Workout</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete this workout? This will not delete the activity logs.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="deleteWorkout">Delete</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
