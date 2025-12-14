<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { Activity, ActivityLog, AppPageProps } from '@/types';

import { index, create } from '@/actions/App/Http/Controllers/ActivityController';
import { index as activityTypesIndex } from '@/actions/App/Http/Controllers/ActivityTypeController';
import { index as logsIndex, create as createLog } from '@/actions/App/Http/Controllers/ActivityLogController';
import { edit as familyEdit } from '@/actions/App/Http/Controllers/FamilyController';
import { index as memberStatsIndex } from '@/actions/App/Http/Controllers/MemberStatsController';
import { index as workoutsIndex, create as createWorkout, show as showWorkout } from '@/actions/App/Http/Controllers/WorkoutController';

defineProps<{
    user: {
        id: number;
        name: string;
        email: string | null;
    };
    recentActivities: Activity[];
    recentLogs: ActivityLog[];
    activityStats: {
        total: number;
        logsThisWeek: number;
        byType: { name: string; color: string; count: number }[];
    };
}>();

const page = usePage<AppPageProps>();
const activeWorkout = computed(() => page.props.activeWorkout);

function logout() {
    router.post('/logout');
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString(undefined, {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
}

function formatMetrics(log: ActivityLog): string {
    const parts = [];
    if (log.sets && log.reps) {
        parts.push(`${log.sets}×${log.reps}`);
    }
    if (log.weight) {
        parts.push(`${log.weight} lbs`);
    }
    if (log.duration_seconds) {
        const mins = Math.floor(log.duration_seconds / 60);
        parts.push(`${mins} min`);
    }
    return parts.join(' · ') || '';
}
</script>

<template>
    <Head title="Dashboard" />
    <div class="min-h-screen bg-background" :class="{ 'pt-12': activeWorkout }">
        <nav class="border-b bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <span class="text-lg font-semibold text-foreground">RepLog</span>
                        <Link
                            href="/dashboard"
                            class="text-sm font-medium text-foreground"
                        >
                            Dashboard
                        </Link>
                        <Link
                            :href="index.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Activities
                        </Link>
                        <Link
                            :href="workoutsIndex.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Workouts
                        </Link>
                        <Link
                            :href="logsIndex.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Logs
                        </Link>
                        <Link
                            :href="activityTypesIndex.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Categories
                        </Link>
                        <Link
                            :href="familyEdit.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Family
                        </Link>
                        <Link
                            :href="memberStatsIndex.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Stats
                        </Link>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-muted-foreground">{{ user.name }}</span>
                        <Button variant="ghost" size="sm" @click="logout">Logout</Button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Dashboard</h1>
                    <p class="mt-1 text-muted-foreground">
                        Welcome back, {{ user.name }}!
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link v-if="activeWorkout" :href="showWorkout.url({ workout: activeWorkout.id })">
                        <Button>Continue Workout</Button>
                    </Link>
                    <Link v-else :href="createWorkout.url()">
                        <Button>Start Workout</Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Activity Stats Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Activities</CardDescription>
                        <CardTitle class="text-4xl">{{ activityStats.total }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-2">
                            <Badge
                                v-for="stat in activityStats.byType"
                                :key="stat.name"
                                :style="{ backgroundColor: stat.color, color: 'white' }"
                            >
                                {{ stat.name }}: {{ stat.count }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Logs This Week Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Logs This Week</CardDescription>
                        <CardTitle class="text-4xl">{{ activityStats.logsThisWeek }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Link :href="logsIndex.url()">
                            <Button variant="outline" size="sm" class="w-full">View All Logs</Button>
                        </Link>
                    </CardContent>
                </Card>

                <!-- Quick Actions Card -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>Common tasks</CardDescription>
                    </CardHeader>
                    <CardContent class="grid grid-cols-2 gap-2">
                        <Link v-if="activeWorkout" :href="showWorkout.url({ workout: activeWorkout.id })">
                            <Button variant="outline" class="w-full justify-start">
                                Continue Workout
                            </Button>
                        </Link>
                        <Link v-else :href="createWorkout.url()">
                            <Button variant="outline" class="w-full justify-start">
                                + Start Workout
                            </Button>
                        </Link>
                        <Link :href="createLog.url()">
                            <Button variant="outline" class="w-full justify-start">
                                + Log Activity
                            </Button>
                        </Link>
                        <Link :href="create.url()">
                            <Button variant="outline" class="w-full justify-start">
                                + Add Activity
                            </Button>
                        </Link>
                        <Link :href="workoutsIndex.url()">
                            <Button variant="outline" class="w-full justify-start">
                                Workout History
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <!-- Recent Logs Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Logs</CardTitle>
                        <CardDescription>Your latest activity logs</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentLogs.length > 0" class="space-y-3">
                            <div
                                v-for="log in recentLogs"
                                :key="log.id"
                                class="flex items-center justify-between"
                            >
                                <div>
                                    <div class="font-medium">{{ log.activity?.name }}</div>
                                    <p class="text-sm text-muted-foreground">
                                        {{ log.user?.name }} · {{ formatDate(log.performed_at) }}
                                        <span v-if="formatMetrics(log)"> · {{ formatMetrics(log) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-4 text-center text-muted-foreground">
                            No activity logs yet.
                            <Link :href="createLog.url()" class="text-primary hover:underline">
                                Log your first activity
                            </Link>
                        </div>
                    </CardContent>
                    <CardFooter v-if="recentLogs.length > 0">
                        <Link :href="logsIndex.url()" class="w-full">
                            <Button variant="outline" class="w-full">View All Logs</Button>
                        </Link>
                    </CardFooter>
                </Card>

                <!-- Recent Activities Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activities</CardTitle>
                        <CardDescription>Latest additions to your library</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentActivities.length > 0" class="space-y-3">
                            <div
                                v-for="activity in recentActivities"
                                :key="activity.id"
                                class="flex items-center justify-between"
                            >
                                <div>
                                    <Link
                                        :href="`/activities/${activity.id}`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ activity.name }}
                                    </Link>
                                    <p class="text-sm text-muted-foreground">
                                        {{ activity.muscle_group ?? 'No target area' }}
                                    </p>
                                </div>
                                <Badge
                                    v-if="activity.activity_type"
                                    :style="{ backgroundColor: activity.activity_type.color, color: 'white' }"
                                >
                                    {{ activity.activity_type.name }}
                                </Badge>
                            </div>
                        </div>
                        <div v-else class="py-4 text-center text-muted-foreground">
                            No activities yet.
                            <Link :href="create.url()" class="text-primary hover:underline">
                                Add your first activity
                            </Link>
                        </div>
                    </CardContent>
                    <CardFooter v-if="activityStats.total > 5">
                        <Link :href="index.url()" class="w-full">
                            <Button variant="outline" class="w-full">
                                View All {{ activityStats.total }} Activities
                            </Button>
                        </Link>
                    </CardFooter>
                </Card>
            </div>

            <!-- Full Activity Table -->
            <Card class="mt-6">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Activity Library</CardTitle>
                            <CardDescription>Manage your activities</CardDescription>
                        </div>
                        <Link :href="create.url()">
                            <Button>Add Activity</Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table v-if="recentActivities.length > 0">
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Category</TableHead>
                                <TableHead>Target Area</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="activity in recentActivities" :key="activity.id">
                                <TableCell class="font-medium">
                                    <Link
                                        :href="`/activities/${activity.id}`"
                                        class="hover:underline"
                                    >
                                        {{ activity.name }}
                                    </Link>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        v-if="activity.activity_type"
                                        :style="{ backgroundColor: activity.activity_type.color, color: 'white' }"
                                    >
                                        {{ activity.activity_type.name }}
                                    </Badge>
                                    <span v-else class="text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell>{{ activity.muscle_group ?? '-' }}</TableCell>
                                <TableCell class="text-right">
                                    <Link :href="`/activities/${activity.id}/edit`">
                                        <Button variant="ghost" size="sm">Edit</Button>
                                    </Link>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <div v-else class="py-8 text-center text-muted-foreground">
                        Your activity library is empty.
                        <Link :href="create.url()" class="text-primary hover:underline">
                            Add your first activity
                        </Link>
                        to get started.
                    </div>
                </CardContent>
                <CardFooter v-if="activityStats.total > 5">
                    <Link :href="index.url()" class="w-full">
                        <Button variant="outline" class="w-full">
                            View All {{ activityStats.total }} Activities
                        </Button>
                    </Link>
                </CardFooter>
            </Card>
        </main>
    </div>
</template>
