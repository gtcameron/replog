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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { Exercise } from '@/types';

import { index, edit, destroy } from '@/actions/App/Http/Controllers/ExerciseController';

const props = defineProps<{
    exercise: Exercise;
}>();

const showDeleteDialog = ref(false);

function deleteExercise() {
    router.delete(destroy.url(props.exercise.id));
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
    <Head :title="exercise.name" />
    <div class="min-h-screen bg-background">
        <nav class="border-b bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <Link :href="index.url()" class="text-lg font-semibold text-foreground">
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
                            Exercises
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div>
                            <CardTitle class="text-2xl">{{ exercise.name }}</CardTitle>
                            <CardDescription class="mt-2 flex items-center gap-2">
                                <Badge variant="secondary">
                                    {{ equipmentLabels[exercise.equipment_type] ?? exercise.equipment_type }}
                                </Badge>
                                <span v-if="exercise.muscle_group">{{ exercise.muscle_group }}</span>
                            </CardDescription>
                        </div>
                        <div class="flex items-center gap-2">
                            <Link :href="edit.url(exercise.id)">
                                <Button variant="outline" size="sm">Edit</Button>
                            </Link>
                            <Button
                                variant="outline"
                                size="sm"
                                class="text-destructive hover:text-destructive"
                                @click="showDeleteDialog = true"
                            >
                                Delete
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div v-if="exercise.description">
                        <h3 class="text-sm font-medium text-muted-foreground">Description</h3>
                        <p class="mt-1 text-foreground">{{ exercise.description }}</p>
                    </div>

                    <div v-if="exercise.instructions">
                        <h3 class="text-sm font-medium text-muted-foreground">Instructions</h3>
                        <p class="mt-1 whitespace-pre-wrap text-foreground">
                            {{ exercise.instructions }}
                        </p>
                    </div>

                    <div
                        v-if="!exercise.description && !exercise.instructions"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No additional details provided for this exercise.
                    </div>
                </CardContent>
            </Card>

            <div class="mt-6">
                <Link :href="index.url()">
                    <Button variant="outline">Back to Exercises</Button>
                </Link>
            </div>
        </main>
    </div>

    <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = false">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Exercise</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete "{{ exercise.name }}"? This action cannot be
                    undone.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    @click="deleteExercise"
                >
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
