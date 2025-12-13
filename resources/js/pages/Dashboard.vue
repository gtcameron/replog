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
import type { Exercise } from '@/types';

import { index, create } from '@/actions/App/Http/Controllers/ExerciseController';

defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
    };
    recentExercises: Exercise[];
    exerciseStats: {
        total: number;
        byEquipment: { type: string; count: number }[];
    };
}>();

function logout() {
    router.post('/logout');
}

const equipmentLabels: Record<string, string> = {
    barbell: 'Barbell',
    dumbbell: 'Dumbbell',
    machine: 'Machine',
    cable: 'Cable',
    bodyweight: 'Bodyweight',
    kettlebell: 'Kettlebell',
    band: 'Resistance Band',
    other: 'Other',
};
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
                            Exercises
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
                <!-- Exercise Stats Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Exercises</CardDescription>
                        <CardTitle class="text-4xl">{{ exerciseStats.total }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-2">
                            <Badge
                                v-for="stat in exerciseStats.byEquipment"
                                :key="stat.type"
                                variant="secondary"
                            >
                                {{ stat.type }}: {{ stat.count }}
                            </Badge>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Link :href="index.url()" class="w-full">
                            <Button variant="outline" class="w-full">View All Exercises</Button>
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
                                + Add New Exercise
                            </Button>
                        </Link>
                        <Link :href="index.url()">
                            <Button variant="outline" class="w-full justify-start">
                                Browse Exercise Library
                            </Button>
                        </Link>
                    </CardContent>
                </Card>

                <!-- Recent Exercises Card -->
                <Card class="md:col-span-2 lg:col-span-1">
                    <CardHeader>
                        <CardTitle>Recent Exercises</CardTitle>
                        <CardDescription>Latest additions to your library</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentExercises.length > 0" class="space-y-3">
                            <div
                                v-for="exercise in recentExercises"
                                :key="exercise.id"
                                class="flex items-center justify-between"
                            >
                                <div>
                                    <Link
                                        :href="`/exercises/${exercise.id}`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ exercise.name }}
                                    </Link>
                                    <p class="text-sm text-muted-foreground">
                                        {{ exercise.muscle_group ?? 'No muscle group' }}
                                    </p>
                                </div>
                                <Badge variant="outline">
                                    {{ equipmentLabels[exercise.equipment_type] ?? exercise.equipment_type }}
                                </Badge>
                            </div>
                        </div>
                        <div v-else class="py-4 text-center text-muted-foreground">
                            No exercises yet.
                            <Link :href="create.url()" class="text-primary hover:underline">
                                Add your first exercise
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Full Exercise Table -->
            <Card class="mt-6">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Exercise Library</CardTitle>
                            <CardDescription>Manage your exercises</CardDescription>
                        </div>
                        <Link :href="create.url()">
                            <Button>Add Exercise</Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table v-if="recentExercises.length > 0">
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Equipment</TableHead>
                                <TableHead>Muscle Group</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="exercise in recentExercises" :key="exercise.id">
                                <TableCell class="font-medium">
                                    <Link
                                        :href="`/exercises/${exercise.id}`"
                                        class="hover:underline"
                                    >
                                        {{ exercise.name }}
                                    </Link>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="secondary">
                                        {{ equipmentLabels[exercise.equipment_type] ?? exercise.equipment_type }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ exercise.muscle_group ?? '-' }}</TableCell>
                                <TableCell class="text-right">
                                    <Link :href="`/exercises/${exercise.id}/edit`">
                                        <Button variant="ghost" size="sm">Edit</Button>
                                    </Link>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <div v-else class="py-8 text-center text-muted-foreground">
                        Your exercise library is empty.
                        <Link :href="create.url()" class="text-primary hover:underline">
                            Add your first exercise
                        </Link>
                        to get started.
                    </div>
                </CardContent>
                <CardFooter v-if="exerciseStats.total > 5">
                    <Link :href="index.url()" class="w-full">
                        <Button variant="outline" class="w-full">
                            View All {{ exerciseStats.total }} Exercises
                        </Button>
                    </Link>
                </CardFooter>
            </Card>
        </main>
    </div>
</template>
