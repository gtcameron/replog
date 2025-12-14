<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { FamilyMember, Workout, AppPageProps } from '@/types';

import { index, show, update } from '@/actions/App/Http/Controllers/WorkoutController';

const props = defineProps<{
    workout: Workout;
    members: FamilyMember[];
}>();

const page = usePage<AppPageProps>();
const activeWorkout = computed(() => page.props.activeWorkout);

function formatDateTimeLocal(dateString: string | null): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    // Format as YYYY-MM-DDTHH:mm for datetime-local input
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

const form = useForm({
    name: props.workout.name || '',
    notes: props.workout.notes || '',
    started_at: formatDateTimeLocal(props.workout.started_at),
    ended_at: formatDateTimeLocal(props.workout.ended_at),
});

function submit() {
    form.transform((data) => ({
        name: data.name || null,
        notes: data.notes || null,
        started_at: data.started_at ? new Date(data.started_at).toISOString() : null,
        ended_at: data.ended_at ? new Date(data.ended_at).toISOString() : null,
    })).put(update.url({ workout: props.workout.id }));
}

function formatDuration(): string {
    if (!form.started_at || !form.ended_at) return '-';
    const start = new Date(form.started_at).getTime();
    const end = new Date(form.ended_at).getTime();
    const seconds = Math.floor((end - start) / 1000);
    if (seconds < 0) return 'Invalid';
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    if (hrs > 0) {
        return `${hrs}h ${mins}m`;
    }
    return `${mins}m`;
}
</script>

<template>
    <Head title="Edit Workout" />
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
                        <Link :href="index.url()" class="text-sm text-muted-foreground hover:text-foreground">
                            Workouts
                        </Link>
                        <Link :href="show.url({ workout: workout.id })" class="text-sm text-muted-foreground hover:text-foreground">
                            {{ workout.name || 'Workout' }}
                        </Link>
                        <span class="text-sm font-medium text-foreground">Edit</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <CardTitle>Edit Workout</CardTitle>
                    <CardDescription>
                        Update workout details including times and notes.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Workout Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="e.g., Morning Strength, Leg Day"
                            />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="started_at">Start Time</Label>
                                <Input
                                    id="started_at"
                                    v-model="form.started_at"
                                    type="datetime-local"
                                />
                                <p v-if="form.errors.started_at" class="text-sm text-destructive">
                                    {{ form.errors.started_at }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="ended_at">End Time</Label>
                                <Input
                                    id="ended_at"
                                    v-model="form.ended_at"
                                    type="datetime-local"
                                />
                                <p v-if="form.errors.ended_at" class="text-sm text-destructive">
                                    {{ form.errors.ended_at }}
                                </p>
                            </div>
                        </div>

                        <div v-if="form.started_at && form.ended_at" class="rounded-lg border bg-muted/30 p-3">
                            <p class="text-sm text-muted-foreground">
                                Duration: <span class="font-medium text-foreground">{{ formatDuration() }}</span>
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="notes">Notes</Label>
                            <Textarea
                                id="notes"
                                v-model="form.notes"
                                placeholder="Any notes about this workout..."
                                rows="3"
                            />
                            <p v-if="form.errors.notes" class="text-sm text-destructive">
                                {{ form.errors.notes }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                Save Changes
                            </Button>
                            <Link :href="show.url({ workout: workout.id })">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
