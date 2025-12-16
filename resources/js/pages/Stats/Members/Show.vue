<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { VisXYContainer, VisLine, VisAxis, VisScatter } from '@unovis/vue';
import { computed } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartTooltip, ChartTooltipContent, type ChartConfig } from '@/components/ui/chart';
import type { ActivityProgression, FamilyMember } from '@/types';

import { index } from '@/actions/App/Http/Controllers/MemberStatsController';

const props = defineProps<{
    member: FamilyMember;
    progressions: ActivityProgression[];
}>();

function getMetricLabel(activity: ActivityProgression['activity']): string {
    if (activity.tracks_weight) return 'Weight (lbs)';
    if (activity.tracks_duration) return 'Duration (sec)';
    if (activity.tracks_distance) return 'Distance (mi)';
    if (activity.tracks_reps) return 'Reps';
    return 'Value';
}

function formatValue(value: number | null, activity: ActivityProgression['activity']): string {
    if (value === null) return '-';
    if (activity.tracks_weight) return `${value} lbs`;
    if (activity.tracks_duration) {
        const mins = Math.floor(value / 60);
        const secs = value % 60;
        return mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
    }
    if (activity.tracks_distance) return `${value} mi`;
    return String(value);
}

function getChartConfig(activity: ActivityProgression['activity']): ChartConfig {
    return {
        value: {
            label: getMetricLabel(activity),
            color: activity.activity_type?.color || 'hsl(var(--primary))',
        },
    };
}

// Transform data for Unovis
function getChartData(progression: ActivityProgression) {
    return progression.data
        .filter((point) => point.value !== null)
        .map((point, index) => ({
            x: index,
            y: point.value as number,
            date: point.date,
        }));
}
</script>

<template>
    <Head :title="`${member.name}'s Progress`" />
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
                            Stats
                        </Link>
                        <span class="text-sm font-medium text-foreground">{{ member.name }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">{{ member.name }}'s Progress</h1>
                    <p class="mt-1 text-muted-foreground">
                        Progression over time in each activity
                    </p>
                </div>
                <Link :href="index.url()">
                    <Button variant="outline">Back to Members</Button>
                </Link>
            </div>

            <div v-if="progressions.length > 0" class="grid gap-6">
                <Card v-for="progression in progressions" :key="progression.activity.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    {{ progression.activity.name }}
                                    <Badge
                                        v-if="progression.activity.activity_type"
                                        :style="{
                                            backgroundColor: progression.activity.activity_type.color,
                                            color: 'white',
                                        }"
                                    >
                                        {{ progression.activity.activity_type.name }}
                                    </Badge>
                                </CardTitle>
                                <CardDescription>
                                    {{ progression.totalSessions }} sessions logged
                                </CardDescription>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-muted-foreground">Personal Best</div>
                                <div class="text-xl font-bold text-foreground">
                                    {{ formatValue(progression.personalBest, progression.activity) }}
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="md:col-span-2">
                                <ChartContainer
                                    v-if="getChartData(progression).length > 1"
                                    :config="getChartConfig(progression.activity)"
                                    class="h-[200px]"
                                >
                                    <VisXYContainer :data="getChartData(progression)" :margin="{ top: 10, right: 10, bottom: 30, left: 40 }">
                                        <VisLine
                                            :x="(d: { x: number }) => d.x"
                                            :y="(d: { y: number }) => d.y"
                                            :color="progression.activity.activity_type?.color || 'hsl(var(--primary))'"
                                        />
                                        <VisScatter
                                            :x="(d: { x: number }) => d.x"
                                            :y="(d: { y: number }) => d.y"
                                            :size="4"
                                            :color="progression.activity.activity_type?.color || 'hsl(var(--primary))'"
                                        />
                                        <VisAxis type="x" :tickFormat="(i: number) => getChartData(progression)[i]?.date?.slice(5) || ''" />
                                        <VisAxis type="y" />
                                    </VisXYContainer>
                                </ChartContainer>
                                <div
                                    v-else
                                    class="flex h-[200px] items-center justify-center rounded-lg border border-dashed text-muted-foreground"
                                >
                                    Not enough data points for chart
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="rounded-lg border p-4">
                                    <div class="text-sm text-muted-foreground">Total Sessions</div>
                                    <div class="text-2xl font-bold">{{ progression.totalSessions }}</div>
                                </div>
                                <div class="rounded-lg border p-4">
                                    <div class="text-sm text-muted-foreground">Last Value</div>
                                    <div class="text-2xl font-bold">
                                        {{ formatValue(progression.lastValue, progression.activity) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card v-else>
                <CardContent class="py-12 text-center text-muted-foreground">
                    {{ member.name }} hasn't logged any activities yet.
                </CardContent>
            </Card>
        </main>
    </div>
</template>
