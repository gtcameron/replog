<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
import type { Activity, WorkoutActivity, FamilyMember, PaginatedData } from '@/types';

import { create, edit, destroy } from '@/actions/App/Http/Controllers/ActivityLogController';

defineProps<{
    logs: PaginatedData<WorkoutActivity>;
    activities: Activity[];
    members: FamilyMember[];
}>();

const logToDelete = ref<WorkoutActivity | null>(null);

function confirmDelete(log: WorkoutActivity) {
    logToDelete.value = log;
}

function deleteLog() {
    if (logToDelete.value) {
        router.delete(destroy.url({ activityLog: logToDelete.value.id }));
        logToDelete.value = null;
    }
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString(undefined, {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
}

function formatMetrics(log: WorkoutActivity): string {
    if (!log.sets || log.sets.length === 0) return '—';

    const setCount = log.sets.length;
    const firstSet = log.sets[0];

    const parts = [];

    if (firstSet.reps !== null) {
        if (setCount > 1) {
            const allSameReps = log.sets.every(s => s.reps === firstSet.reps);
            if (allSameReps) {
                parts.push(`${setCount}×${firstSet.reps}`);
            } else {
                parts.push(`${setCount} sets`);
            }
        } else {
            parts.push(`${firstSet.reps} reps`);
        }
    } else if (setCount > 1) {
        parts.push(`${setCount} sets`);
    }

    const maxWeight = Math.max(...log.sets.map(s => s.weight ?? 0));
    if (maxWeight > 0) {
        parts.push(`${maxWeight} lbs`);
    }

    const maxDuration = Math.max(...log.sets.map(s => s.duration_seconds ?? 0));
    if (maxDuration > 0) {
        const mins = Math.floor(maxDuration / 60);
        const secs = maxDuration % 60;
        parts.push(secs > 0 ? `${mins}:${secs.toString().padStart(2, '0')}` : `${mins} min`);
    }

    const maxDistance = Math.max(...log.sets.map(s => s.distance ?? 0));
    if (maxDistance > 0) {
        parts.push(`${maxDistance} mi`);
    }

    return parts.join(' · ') || '—';
}
</script>

<template>
    <Head title="Activity Logs" />
    <div class="min-h-screen bg-background">
        <nav class="border-b bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <Link href="/dashboard" class="text-lg font-semibold text-foreground">
                            RepLog
                        </Link>
                        <Link
                            href="/dashboard"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href="/activities"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Activities
                        </Link>
                        <span class="text-sm font-medium text-foreground">Logs</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Activity Logs</CardTitle>
                            <CardDescription>Track your completed activities</CardDescription>
                        </div>
                        <Link :href="create.url()">
                            <Button>Log Activity</Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Date</TableHead>
                                <TableHead>Activity</TableHead>
                                <TableHead>Member</TableHead>
                                <TableHead>Metrics</TableHead>
                                <TableHead>Notes</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="log in logs.data" :key="log.id">
                                <TableCell class="font-medium">
                                    {{ formatDate(log.performed_at) }}
                                </TableCell>
                                <TableCell>{{ log.activity?.name }}</TableCell>
                                <TableCell>{{ log.user?.name }}</TableCell>
                                <TableCell class="text-muted-foreground">
                                    {{ formatMetrics(log) }}
                                </TableCell>
                                <TableCell class="max-w-xs truncate text-muted-foreground">
                                    {{ log.notes || '—' }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="edit.url({ activityLog: log.id })">
                                            <Button variant="outline" size="sm">Edit</Button>
                                        </Link>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="confirmDelete(log)"
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="logs.data.length === 0">
                                <TableCell colspan="6" class="text-center text-muted-foreground py-8">
                                    No activity logs yet. Start tracking your activities!
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </main>
    </div>

    <AlertDialog :open="logToDelete !== null" @update:open="logToDelete = null">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Activity Log</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete this log entry? This action cannot be undone.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="deleteLog">Delete</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
