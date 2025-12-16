<script setup lang="ts">
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { VisXYContainer, VisLine, VisAxis, VisScatter } from '@unovis/vue';
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { Plus, Trash2 } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, type ChartConfig } from '@/components/ui/chart';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import type { Activity, WorkoutActivity, FamilyMember, Workout, ActivityHistoryData, ProgressionPoint, AppPageProps } from '@/types';

import { index, end, edit } from '@/actions/App/Http/Controllers/WorkoutController';
import { store, activityHistory } from '@/actions/App/Http/Controllers/WorkoutActivityLogController';
import { create as createActivity } from '@/actions/App/Http/Controllers/ActivityController';

interface SetData {
    set_number: number;
    reps: string;
    weight: string;
    duration_seconds: string;
    distance: string;
}

const props = defineProps<{
    workout: Workout;
    activities: Activity[];
    members: FamilyMember[];
}>();

const page = usePage<AppPageProps>();
const activeWorkout = computed(() => page.props?.activeWorkout ?? null);
const isActive = computed(() => props.workout.ended_at === null);

// Timer
const elapsedSeconds = ref(0);
let timerInterval: ReturnType<typeof setInterval> | null = null;

function updateTimer() {
    const startTime = new Date(props.workout.started_at).getTime();
    elapsedSeconds.value = Math.floor((Date.now() - startTime) / 1000);
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
    if (isActive.value) {
        timerInterval = setInterval(updateTimer, 1000);
    }
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

// Activity form
const sets = ref<SetData[]>([
    { set_number: 1, reps: '', weight: '', duration_seconds: '', distance: '' },
]);

const form = useForm({
    activity_id: '',
    notes: '',
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

// Activity history
const historyData = ref<ActivityHistoryData | null>(null);
const loadingHistory = ref(false);

watch(() => form.activity_id, async (newId) => {
    if (!newId) {
        historyData.value = null;
        return;
    }

    loadingHistory.value = true;
    try {
        const response = await fetch(activityHistory.url({
            workout: props.workout.id,
            activity: parseInt(newId),
        }));
        historyData.value = await response.json();
    } catch (error) {
        console.error('Failed to load activity history:', error);
        historyData.value = null;
    } finally {
        loadingHistory.value = false;
    }
});

function getChartData(chartData: ProgressionPoint[]) {
    return chartData
        .filter((point) => point.value !== null)
        .map((point, index) => ({
            x: index,
            y: point.value as number,
            date: point.date,
        }));
}

function getChartConfig(): ChartConfig {
    return {
        value: {
            label: getMetricLabel(),
            color: 'hsl(var(--primary))',
        },
    };
}

function getMetricLabel(): string {
    if (!selectedActivity.value) return 'Value';
    if (selectedActivity.value.tracks_weight) return 'Weight (lbs)';
    if (selectedActivity.value.tracks_duration) return 'Duration (sec)';
    if (selectedActivity.value.tracks_distance) return 'Distance (mi)';
    if (selectedActivity.value.tracks_reps) return 'Reps';
    return 'Value';
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
        notes: data.notes || null,
        sets: transformedSets,
    })).post(store.url({ workout: props.workout.id }), {
        onSuccess: () => {
            form.reset();
            sets.value = [{ set_number: 1, reps: '', weight: '', duration_seconds: '', distance: '' }];
            historyData.value = null;
        },
    });
}

function endWorkout() {
    router.post(end.url({ workout: props.workout.id }));
}

function formatLogMetrics(log: WorkoutActivity): string {
    if (!log.sets || log.sets.length === 0) return '-';

    const setCount = log.sets.length;
    const parts = [];

    // Get max values across sets
    const maxReps = Math.max(...log.sets.map(s => s.reps ?? 0));
    const maxWeight = Math.max(...log.sets.map(s => s.weight ?? 0));
    const maxDuration = Math.max(...log.sets.map(s => s.duration_seconds ?? 0));
    const maxDistance = Math.max(...log.sets.map(s => s.distance ?? 0));

    if (setCount > 1 && maxReps > 0) {
        const firstSet = log.sets[0];
        const allSameReps = log.sets.every(s => s.reps === firstSet.reps);
        if (allSameReps && firstSet.reps) {
            parts.push(`${setCount}x${firstSet.reps}`);
        } else {
            parts.push(`${setCount} sets`);
        }
    } else if (maxReps > 0) {
        parts.push(`${maxReps} reps`);
    }

    if (maxWeight > 0) {
        parts.push(`${maxWeight} lbs`);
    }
    if (maxDuration > 0) {
        const mins = Math.floor(maxDuration / 60);
        const secs = maxDuration % 60;
        parts.push(secs > 0 ? `${mins}:${secs.toString().padStart(2, '0')}` : `${mins} min`);
    }
    if (maxDistance > 0) {
        parts.push(`${maxDistance} mi`);
    }
    return parts.join(' / ') || '-';
}

function formatHistoryDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
    });
}

function formatHistorySets(entrySets: { set_number: number; reps: number | null; weight: number | null; duration_seconds: number | null; distance: number | null }[]): string {
    if (!entrySets || entrySets.length === 0) return '-';

    const setCount = entrySets.length;
    const parts = [];

    const maxReps = Math.max(...entrySets.map(s => s.reps ?? 0));
    const maxWeight = Math.max(...entrySets.map(s => s.weight ?? 0));
    const maxDuration = Math.max(...entrySets.map(s => s.duration_seconds ?? 0));
    const maxDistance = Math.max(...entrySets.map(s => s.distance ?? 0));

    if (setCount > 1 && maxReps > 0) {
        const firstSet = entrySets[0];
        const allSameReps = entrySets.every(s => s.reps === firstSet.reps);
        if (allSameReps && firstSet.reps) {
            parts.push(`${setCount}x${firstSet.reps}`);
        } else {
            parts.push(`${setCount} sets`);
        }
    } else if (maxReps > 0) {
        parts.push(`${maxReps} reps`);
    }

    if (maxWeight > 0) {
        parts.push(`@ ${maxWeight} lbs`);
    }
    if (maxDuration > 0) {
        parts.push(`${Math.floor(maxDuration / 60)}m`);
    }
    if (maxDistance > 0) {
        parts.push(`${maxDistance} mi`);
    }

    return parts.join(' ') || '-';
}
</script>

<template>
    <Head :title="workout.name || 'Workout'" />
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
                        <span class="text-sm font-medium text-foreground">
                            {{ workout.name || 'Workout' }}
                        </span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Workout Header -->
            <Card class="mb-6">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-3">
                                {{ workout.name || 'Workout' }}
                                <Badge v-if="isActive" variant="default">Active</Badge>
                                <Badge v-else variant="secondary">Completed</Badge>
                            </CardTitle>
                            <CardDescription>
                                {{ workout.user?.name }} &middot;
                                {{ new Date(workout.started_at).toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' }) }}
                            </CardDescription>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold font-mono tabular-nums">
                                {{ formatTime(elapsedSeconds) }}
                            </div>
                            <div class="text-sm text-muted-foreground">
                                {{ workout.workout_activities?.length || 0 }} activities logged
                            </div>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2">
                        <Button v-if="isActive" variant="destructive" @click="endWorkout">
                            End Workout
                        </Button>
                        <Link :href="edit.url({ workout: workout.id })">
                            <Button variant="outline">
                                Edit Workout
                            </Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Add Activity Form -->
                <Card v-if="isActive">
                    <CardHeader>
                        <CardTitle>Add Activity</CardTitle>
                        <CardDescription>Log an activity for this workout</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="activity_id">Activity</Label>
                                <Select v-model="form.activity_id">
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
                                <Link :href="createActivity.url()" class="text-xs text-muted-foreground hover:text-foreground">
                                    + Create new activity
                                </Link>
                            </div>

                            <!-- Activity History -->
                            <div v-if="selectedActivity && historyData" class="rounded-lg border p-4 bg-muted/30">
                                <h4 class="text-sm font-medium mb-3">Recent History</h4>

                                <div v-if="historyData.recentLogs.length > 0" class="space-y-2 mb-4">
                                    <div
                                        v-for="log in historyData.recentLogs"
                                        :key="log.id"
                                        class="flex items-center justify-between text-sm"
                                    >
                                        <span class="text-muted-foreground">{{ formatHistoryDate(log.performed_at) }}</span>
                                        <span class="font-medium">
                                            {{ formatHistorySets(log.sets) }}
                                        </span>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-muted-foreground mb-4">
                                    No previous logs for this activity
                                </div>

                                <!-- Mini Chart -->
                                <div v-if="historyData.chartData.length > 1">
                                    <ChartContainer :config="getChartConfig()" class="h-[100px]">
                                        <VisXYContainer
                                            :data="getChartData(historyData.chartData)"
                                            :margin="{ top: 5, right: 5, bottom: 20, left: 30 }"
                                        >
                                            <VisLine
                                                :x="(d: { x: number }) => d.x"
                                                :y="(d: { y: number }) => d.y"
                                                color="hsl(var(--primary))"
                                            />
                                            <VisScatter
                                                :x="(d: { x: number }) => d.x"
                                                :y="(d: { y: number }) => d.y"
                                                :size="3"
                                                color="hsl(var(--primary))"
                                            />
                                            <VisAxis type="y" :numTicks="3" />
                                        </VisXYContainer>
                                    </ChartContainer>
                                    <p class="text-xs text-center text-muted-foreground mt-1">Last 3 months</p>
                                </div>
                            </div>

                            <div v-if="loadingHistory" class="rounded-lg border p-4 bg-muted/30">
                                <p class="text-sm text-muted-foreground">Loading history...</p>
                            </div>

                            <!-- Sets -->
                            <div v-if="selectedActivity && hasAnyMetrics" class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium">Sets</h4>
                                    <Button type="button" variant="outline" size="sm" @click="addSet">
                                        <Plus class="h-4 w-4 mr-1" />
                                        Add Set
                                    </Button>
                                </div>

                                <div class="space-y-3">
                                    <div
                                        v-for="(set, idx) in sets"
                                        :key="idx"
                                        class="flex items-start gap-2 p-2 rounded-lg border bg-muted/30"
                                    >
                                        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-medium shrink-0 mt-1">
                                            {{ set.set_number }}
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 flex-1">
                                            <div v-if="selectedActivity.tracks_reps" class="space-y-1">
                                                <Label :for="`reps-${idx}`" class="text-xs">Reps</Label>
                                                <Input
                                                    :id="`reps-${idx}`"
                                                    v-model="set.reps"
                                                    type="number"
                                                    min="1"
                                                    placeholder="10"
                                                    class="h-8"
                                                />
                                            </div>

                                            <div v-if="selectedActivity.tracks_weight" class="space-y-1">
                                                <Label :for="`weight-${idx}`" class="text-xs">Weight</Label>
                                                <Input
                                                    :id="`weight-${idx}`"
                                                    v-model="set.weight"
                                                    type="number"
                                                    step="0.5"
                                                    min="0"
                                                    placeholder="135"
                                                    class="h-8"
                                                />
                                            </div>

                                            <div v-if="selectedActivity.tracks_duration" class="space-y-1">
                                                <Label :for="`duration-${idx}`" class="text-xs">Min</Label>
                                                <Input
                                                    :id="`duration-${idx}`"
                                                    v-model="set.duration_seconds"
                                                    type="number"
                                                    min="1"
                                                    placeholder="30"
                                                    class="h-8"
                                                />
                                            </div>

                                            <div v-if="selectedActivity.tracks_distance" class="space-y-1">
                                                <Label :for="`distance-${idx}`" class="text-xs">Miles</Label>
                                                <Input
                                                    :id="`distance-${idx}`"
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
                                            class="h-6 w-6 shrink-0 text-muted-foreground hover:text-destructive"
                                            @click="removeSet(idx)"
                                        >
                                            <Trash2 class="h-3 w-3" />
                                        </Button>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="notes">Notes (optional)</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    placeholder="Any notes..."
                                    rows="2"
                                />
                            </div>

                            <Button type="submit" :disabled="form.processing || !form.activity_id" class="w-full">
                                Log Activity
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Activities Logged -->
                <Card :class="{ 'lg:col-span-2': !isActive }">
                    <CardHeader>
                        <CardTitle>Activities Logged</CardTitle>
                        <CardDescription>Activities completed during this workout</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table v-if="workout.workout_activities && workout.workout_activities.length > 0">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Activity</TableHead>
                                    <TableHead>Metrics</TableHead>
                                    <TableHead>Notes</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="log in workout.workout_activities" :key="log.id">
                                    <TableCell class="font-medium">
                                        {{ log.activity?.name }}
                                    </TableCell>
                                    <TableCell class="font-mono text-sm">
                                        {{ formatLogMetrics(log) }}
                                    </TableCell>
                                    <TableCell class="max-w-xs truncate text-muted-foreground">
                                        {{ log.notes || '-' }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        <div v-else class="py-8 text-center text-muted-foreground">
                            No activities logged yet
                        </div>
                    </CardContent>
                </Card>
            </div>
        </main>
    </div>
</template>
