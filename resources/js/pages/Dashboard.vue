<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

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
import type { Activity } from '@/types';

import { index, create } from '@/actions/App/Http/Controllers/ActivityController';
import { index as activityTypesIndex } from '@/actions/App/Http/Controllers/ActivityTypeController';

defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
    };
    recentActivities: Activity[];
    activityStats: {
        total: number;
        byType: { name: string; color: string; count: number }[];
    };
}>();

function logout() {
    router.post('/logout');
}
</script>

<template>
    <Head title="Dashboard" />
    <div class="min-h-screen bg-background">
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
                            :href="activityTypesIndex.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Categories
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
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-foreground">Dashboard</h1>
                <p class="mt-1 text-muted-foreground">
                    Welcome back, {{ user.name }}!
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Activity Stats Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Activities</CardDescription>
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
                    <CardFooter>
                        <Link :href="index.url()" class="w-full">
                            <Button variant="outline" class="w-full">View All Activities</Button>
                        </Link>
                    </CardFooter>
                </Card>

                <!-- Quick Actions Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>Common tasks</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-2">
                        <Link :href="create.url()">
                            <Button variant="outline" class="w-full justify-start">
                                + Add New Activity
                            </Button>
                        </Link>
                        <Link :href="index.url()">
                            <Button variant="outline" class="w-full justify-start">
                                Browse Activity Library
                            </Button>
                        </Link>
                        <Link :href="activityTypesIndex.url()">
                            <Button variant="outline" class="w-full justify-start">
                                Manage Categories
                            </Button>
                        </Link>
                    </CardContent>
                </Card>

                <!-- Recent Activities Card -->
                <Card class="md:col-span-2 lg:col-span-1">
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
