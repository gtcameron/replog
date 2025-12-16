<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';

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
import type { Activity, WorkoutActivity, FamilyMember } from '@/types';

import { update, index } from '@/actions/App/Http/Controllers/ActivityLogController';

interface SetData {
    set_number: number;
    reps: string;
    weight: string;
    duration_seconds: string;
    distance: string;
}

const props = defineProps<{
    log: WorkoutActivity;
    activities: Activity[];
    members: FamilyMember[];
}>();

const sets = ref<SetData[]>(
    props.log.sets && props.log.sets.length > 0
        ? props.log.sets.map(s => ({
            set_number: s.set_number,
            reps: s.reps?.toString() || '',
            weight: s.weight?.toString() || '',
            duration_seconds: s.duration_seconds ? Math.floor(s.duration_seconds / 60).toString() : '',
            distance: s.distance?.toString() || '',
        }))
        : [{ set_number: 1, reps: '', weight: '', duration_seconds: '', distance: '' }]
);

const form = useForm({
    activity_id: props.log.activity_id.toString(),
    user_id: props.log.user_id.toString(),
    performed_at: props.log.performed_at.split('T')[0],
    notes: props.log.notes || '',
});

const selectedActivity = computed(() => {
    if (!form.activity_id) return null;
    return props.activities.find(a => a.id.toString() === form.activity_id) || null;
});

const hasAnyMetrics = computed(() => {
    if (!selectedActivity.value) return false;
    return selectedActivity.value.tracks_reps ||
        selectedActivity.value.tracks_weight ||
        selectedActivity.value.tracks_duration ||
        selectedActivity.value.tracks_distance;
});

function addSet() {
    const lastSet = sets.value[sets.value.length - 1];
    sets.value.push({
        set_number: sets.value.length + 1,
        reps: lastSet?.reps || '',
        weight: lastSet?.weight || '',
        duration_seconds: lastSet?.duration_seconds || '',
        distance: lastSet?.distance || '',
    });
}

function removeSet(index: number) {
    if (sets.value.length > 1) {
        sets.value.splice(index, 1);
        sets.value.forEach((set, i) => {
            set.set_number = i + 1;
        });
    }
}

function submit() {
    const transformedSets = sets.value.map(set => ({
        set_number: set.set_number,
        reps: set.reps ? parseInt(set.reps) : null,
        weight: set.weight ? parseFloat(set.weight) : null,
        duration_seconds: set.duration_seconds ? parseInt(set.duration_seconds) * 60 : null,
        distance: set.distance ? parseFloat(set.distance) : null,
    }));

    form.transform((data) => ({
        activity_id: parseInt(data.activity_id) || null,
        user_id: parseInt(data.user_id) || null,
        performed_at: data.performed_at,
        notes: data.notes || null,
        sets: transformedSets,
    })).put(update.url({ activityLog: props.log.id }));
}
</script>

<template>
    <Head title="Edit Activity Log" />
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
                    <CardTitle>Edit Activity Log</CardTitle>
                    <CardDescription>Update the log entry details</CardDescription>
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
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium">Sets</h3>
                                <Button type="button" variant="outline" size="sm" @click="addSet">
                                    <Plus class="h-4 w-4 mr-1" />
                                    Add Set
                                </Button>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="(set, index) in sets"
                                    :key="index"
                                    class="flex items-start gap-3 p-3 rounded-lg border bg-muted/30"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary/10 text-primary text-sm font-medium shrink-0">
                                        {{ set.set_number }}
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 flex-1">
                                        <div v-if="selectedActivity.tracks_reps" class="space-y-1">
                                            <Label :for="`reps-${index}`" class="text-xs">Reps</Label>
                                            <Input
                                                :id="`reps-${index}`"
                                                v-model="set.reps"
                                                type="number"
                                                min="1"
                                                placeholder="10"
                                                class="h-8"
                                            />
                                        </div>

                                        <div v-if="selectedActivity.tracks_weight" class="space-y-1">
                                            <Label :for="`weight-${index}`" class="text-xs">Weight (lbs)</Label>
                                            <Input
                                                :id="`weight-${index}`"
                                                v-model="set.weight"
                                                type="number"
                                                step="0.5"
                                                min="0"
                                                placeholder="135"
                                                class="h-8"
                                            />
                                        </div>

                                        <div v-if="selectedActivity.tracks_duration" class="space-y-1">
                                            <Label :for="`duration-${index}`" class="text-xs">Duration (min)</Label>
                                            <Input
                                                :id="`duration-${index}`"
                                                v-model="set.duration_seconds"
                                                type="number"
                                                min="1"
                                                placeholder="30"
                                                class="h-8"
                                            />
                                        </div>

                                        <div v-if="selectedActivity.tracks_distance" class="space-y-1">
                                            <Label :for="`distance-${index}`" class="text-xs">Distance (mi)</Label>
                                            <Input
                                                :id="`distance-${index}`"
                                                v-model="set.distance"
                                                type="number"
                                                step="0.1"
                                                min="0"
                                                placeholder="3.5"
                                                class="h-8"
                                            />
                                        </div>
                                    </div>

                                    <Button
                                        v-if="sets.length > 1"
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8 shrink-0 text-muted-foreground hover:text-destructive"
                                        @click="removeSet(index)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
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
                                Save Changes
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
