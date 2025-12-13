<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Activity, FamilyMember } from '@/types';

import { store, index } from '@/actions/App/Http/Controllers/ActivityLogController';

const props = defineProps<{
    activities: Activity[];
    members: FamilyMember[];
    defaultUserId: number;
}>();

const today = new Date().toISOString().split('T')[0];

const form = useForm({
    activity_id: '',
    user_id: props.defaultUserId.toString(),
    performed_at: today,
    sets: '',
    reps: '',
    weight: '',
    duration_seconds: '',
    distance: '',
    notes: '',
});

const selectedActivity = computed(() => {
    if (!form.activity_id) return null;
    return props.activities.find(a => a.id.toString() === form.activity_id) || null;
});

const hasAnyMetrics = computed(() => {
    if (!selectedActivity.value) return false;
    return selectedActivity.value.tracks_sets ||
        selectedActivity.value.tracks_reps ||
        selectedActivity.value.tracks_weight ||
        selectedActivity.value.tracks_duration ||
        selectedActivity.value.tracks_distance;
});

function submit() {
    form.transform((data) => ({
        ...data,
        activity_id: parseInt(data.activity_id) || null,
        user_id: parseInt(data.user_id) || null,
        sets: data.sets ? parseInt(data.sets) : null,
        reps: data.reps ? parseInt(data.reps) : null,
        weight: data.weight ? parseFloat(data.weight) : null,
        duration_seconds: data.duration_seconds ? parseInt(data.duration_seconds) * 60 : null,
        distance: data.distance ? parseFloat(data.distance) : null,
    })).post(store.url());
}
</script>

<template>
    <Head title="Log Activity" />
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
                            :href="index.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Logs
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <CardTitle>Log Activity</CardTitle>
                    <CardDescription>Record a completed activity</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="activity_id">Activity</Label>
                                <Select v-model="form.activity_id" required>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select an activity" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="activity in activities"
                                            :key="activity.id"
                                            :value="activity.id.toString()"
                                        >
                                            {{ activity.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.activity_id" class="text-sm text-destructive">
                                    {{ form.errors.activity_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="user_id">Who did it?</Label>
                                <Select v-model="form.user_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select member" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="member in members"
                                            :key="member.id"
                                            :value="member.id.toString()"
                                        >
                                            {{ member.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.user_id" class="text-sm text-destructive">
                                    {{ form.errors.user_id }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="performed_at">Date</Label>
                            <Input
                                id="performed_at"
                                v-model="form.performed_at"
                                type="date"
                                required
                            />
                            <p v-if="form.errors.performed_at" class="text-sm text-destructive">
                                {{ form.errors.performed_at }}
                            </p>
                        </div>

                        <div v-if="selectedActivity && hasAnyMetrics" class="border-t pt-6">
                            <h3 class="text-sm font-medium mb-4">Metrics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div v-if="selectedActivity.tracks_sets" class="space-y-2">
                                    <Label for="sets">Sets</Label>
                                    <Input
                                        id="sets"
                                        v-model="form.sets"
                                        type="number"
                                        min="1"
                                        placeholder="e.g. 3"
                                    />
                                    <p v-if="form.errors.sets" class="text-sm text-destructive">
                                        {{ form.errors.sets }}
                                    </p>
                                </div>

                                <div v-if="selectedActivity.tracks_reps" class="space-y-2">
                                    <Label for="reps">Reps</Label>
                                    <Input
                                        id="reps"
                                        v-model="form.reps"
                                        type="number"
                                        min="1"
                                        placeholder="e.g. 10"
                                    />
                                    <p v-if="form.errors.reps" class="text-sm text-destructive">
                                        {{ form.errors.reps }}
                                    </p>
                                </div>

                                <div v-if="selectedActivity.tracks_weight" class="space-y-2">
                                    <Label for="weight">Weight (lbs)</Label>
                                    <Input
                                        id="weight"
                                        v-model="form.weight"
                                        type="number"
                                        step="0.5"
                                        min="0"
                                        placeholder="e.g. 135"
                                    />
                                    <p v-if="form.errors.weight" class="text-sm text-destructive">
                                        {{ form.errors.weight }}
                                    </p>
                                </div>

                                <div v-if="selectedActivity.tracks_duration" class="space-y-2">
                                    <Label for="duration_seconds">Duration (minutes)</Label>
                                    <Input
                                        id="duration_seconds"
                                        v-model="form.duration_seconds"
                                        type="number"
                                        min="1"
                                        placeholder="e.g. 30"
                                    />
                                    <p v-if="form.errors.duration_seconds" class="text-sm text-destructive">
                                        {{ form.errors.duration_seconds }}
                                    </p>
                                </div>

                                <div v-if="selectedActivity.tracks_distance" class="space-y-2" :class="{ 'col-span-2': !selectedActivity.tracks_duration }">
                                    <Label for="distance">Distance (miles)</Label>
                                    <Input
                                        id="distance"
                                        v-model="form.distance"
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        placeholder="e.g. 3.5"
                                    />
                                    <p v-if="form.errors.distance" class="text-sm text-destructive">
                                        {{ form.errors.distance }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="notes">Notes</Label>
                            <Textarea
                                id="notes"
                                v-model="form.notes"
                                placeholder="Any additional notes..."
                                rows="3"
                            />
                            <p v-if="form.errors.notes" class="text-sm text-destructive">
                                {{ form.errors.notes }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                Log Activity
                            </Button>
                            <Link :href="index.url()">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
