<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { VisXYContainer, VisLine, VisAxis, VisScatter } from '@unovis/vue';
import { computed } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, type ChartConfig } from '@/components/ui/chart';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { Activity, ComparisonChartData, FamilyMember, LeaderboardEntry } from '@/types';

import { index } from '@/actions/App/Http/Controllers/ActivityStatsController';

const props = defineProps<{
    activity: Activity;
    leaderboard: LeaderboardEntry[];
    chartData: ComparisonChartData;
    members: FamilyMember[];
}>();

function getMetricLabel(): string {
    if (props.activity.tracks_weight) return 'Weight (lbs)';
    if (props.activity.tracks_duration) return 'Duration (sec)';
    if (props.activity.tracks_distance) return 'Distance (mi)';
    if (props.activity.tracks_reps) return 'Reps';
    return 'Value';
}

function formatValue(value: number | null): string {
    if (value === null) return '-';
    if (props.activity.tracks_weight) return `${value} lbs`;
    if (props.activity.tracks_duration) {
        const mins = Math.floor(value / 60);
        const secs = value % 60;
        return mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
    }
    if (props.activity.tracks_distance) return `${value} mi`;
    return String(value);
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
    });
}

function getRankEmoji(rank: number): string {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';
    return `#${rank}`;
}

// Colors for different members in the chart
const memberColors = [
    'hsl(221, 83%, 53%)', // blue
    'hsl(142, 71%, 45%)', // green
    'hsl(0, 84%, 60%)',   // red
    'hsl(262, 83%, 58%)', // purple
    'hsl(25, 95%, 53%)',  // orange
    'hsl(174, 72%, 56%)', // teal
];

// Transform data for Unovis - create a flattened dataset with member info
const chartConfig = computed<ChartConfig>(() => {
    const config: ChartConfig = {};
    props.chartData.series.forEach((series, index) => {
        config[series.memberName] = {
            label: series.memberName,
            color: memberColors[index % memberColors.length],
        };
    });
    return config;
});

// Transform data for multi-line chart
// Each data point needs x (date index) and y values for each member
const transformedChartData = computed(() => {
    return props.chartData.dates.map((date, dateIndex) => {
        const point: Record<string, number | null | string> = {
            x: dateIndex,
            date: date,
        };
        props.chartData.series.forEach((series) => {
            point[series.memberName] = series.data[dateIndex];
        });
        return point;
    });
});

// Create y accessor functions for each member
const yAccessors = computed(() => {
    return props.chartData.series.map((series) => {
        return (d: Record<string, number | null>) => d[series.memberName] as number | null;
    });
});

const lineColors = computed(() => {
    return props.chartData.series.map((_, index) => memberColors[index % memberColors.length]);
});

const hasChartData = computed(() => {
    return props.chartData.dates.length > 0 && props.chartData.series.length > 0;
});
</script>

<template>
    <Head :title="`${activity.name} Stats`" />
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
                        <span class="text-sm font-medium text-foreground">{{ activity.name }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="flex items-center gap-3 text-2xl font-semibold text-foreground">
                        {{ activity.name }}
                        <Badge
                            v-if="activity.activity_type"
                            :style="{
                                backgroundColor: activity.activity_type.color,
                                color: 'white',
                            }"
                        >
                            {{ activity.activity_type.name }}
                        </Badge>
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Leaderboard and performance comparison
                    </p>
                </div>
                <Link :href="index.url()">
                    <Button variant="outline">Back to Activities</Button>
                </Link>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Leaderboard -->
                <Card>
                    <CardHeader>
                        <CardTitle>Leaderboard</CardTitle>
                        <CardDescription>Ranked by most recent {{ getMetricLabel().toLowerCase() }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table v-if="leaderboard.length > 0">
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-16">Rank</TableHead>
                                    <TableHead>Member</TableHead>
                                    <TableHead class="text-right">{{ getMetricLabel() }}</TableHead>
                                    <TableHead class="text-right">Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="entry in leaderboard" :key="entry.member.id">
                                    <TableCell class="text-lg">
                                        {{ getRankEmoji(entry.rank) }}
                                    </TableCell>
                                    <TableCell class="font-medium">
                                        {{ entry.member.name }}
                                    </TableCell>
                                    <TableCell class="text-right font-mono">
                                        {{ formatValue(entry.value) }}
                                    </TableCell>
                                    <TableCell class="text-right text-muted-foreground">
                                        {{ formatDate(entry.date) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        <div v-else class="py-8 text-center text-muted-foreground">
                            No leaderboard data available yet.
                        </div>
                    </CardContent>
                </Card>

                <!-- Legend -->
                <Card v-if="hasChartData" class="lg:row-start-1 lg:col-start-2">
                    <CardHeader>
                        <CardTitle>Members</CardTitle>
                        <CardDescription>Chart legend</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-4">
                            <div
                                v-for="(series, idx) in chartData.series"
                                :key="series.memberId"
                                class="flex items-center gap-2"
                            >
                                <div
                                    class="h-3 w-3 rounded-full"
                                    :style="{ backgroundColor: memberColors[idx % memberColors.length] }"
                                />
                                <span class="text-sm">{{ series.memberName }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Comparison Chart -->
            <Card class="mt-6">
                <CardHeader>
                    <CardTitle>Progress Comparison</CardTitle>
                    <CardDescription>Compare {{ getMetricLabel().toLowerCase() }} over time</CardDescription>
                </CardHeader>
                <CardContent>
                    <ChartContainer
                        v-if="hasChartData && chartData.dates.length > 1"
                        :config="chartConfig"
                        class="h-[300px]"
                    >
                        <VisXYContainer
                            :data="transformedChartData"
                            :margin="{ top: 20, right: 20, bottom: 40, left: 50 }"
                        >
                            <template v-for="(series, idx) in chartData.series" :key="series.memberId">
                                <VisLine
                                    :x="(d: Record<string, number>) => d.x"
                                    :y="(d: Record<string, number | null>) => d[series.memberName] as number"
                                    :color="memberColors[idx % memberColors.length]"
                                />
                                <VisScatter
                                    :x="(d: Record<string, number>) => d.x"
                                    :y="(d: Record<string, number | null>) => d[series.memberName] as number"
                                    :size="4"
                                    :color="memberColors[idx % memberColors.length]"
                                />
                            </template>
                            <VisAxis
                                type="x"
                                :tickFormat="(i: number) => chartData.dates[i]?.slice(5) || ''"
                            />
                            <VisAxis type="y" />
                        </VisXYContainer>
                    </ChartContainer>
                    <div
                        v-else
                        class="flex h-[300px] items-center justify-center rounded-lg border border-dashed text-muted-foreground"
                    >
                        <span v-if="!hasChartData">No chart data available</span>
                        <span v-else>Not enough data points for chart</span>
                    </div>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
