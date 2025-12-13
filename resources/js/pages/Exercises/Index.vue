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
import type { EquipmentType, Exercise } from '@/types';

import { index, destroy } from '@/actions/App/Http/Controllers/ExerciseController';

const props = defineProps<{
    exercises: Exercise[];
    equipmentTypes: EquipmentType[];
}>();

const selectedEquipment = ref<string>('all');
const exerciseToDelete = ref<Exercise | null>(null);

const filteredExercises = computed(() => {
    if (selectedEquipment.value === 'all') {
        return props.exercises;
    }
    return props.exercises.filter((e) => e.equipment_type === selectedEquipment.value);
});

function getEquipmentLabel(value: string): string {
    return props.equipmentTypes.find((t) => t.value === value)?.label ?? value;
}

function confirmDelete(exercise: Exercise) {
    exerciseToDelete.value = exercise;
}

function deleteExercise() {
    if (exerciseToDelete.value) {
        router.delete(destroy.url(exerciseToDelete.value.id), {
            onSuccess: () => {
                exerciseToDelete.value = null;
            },
        });
    }
}

import { computed } from 'vue';
</script>

<template>
    <Head title="Exercises" />
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
                            class="text-sm font-medium text-foreground"
                        >
                            Exercises
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Exercises</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage your exercise library
                    </p>
                </div>
                <Link :href="'/exercises/create'">
                    <Button>Add Exercise</Button>
                </Link>
            </div>

            <div class="mb-6">
                <Select v-model="selectedEquipment">
                    <SelectTrigger class="w-[200px]">
                        <SelectValue placeholder="Filter by equipment" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Equipment</SelectItem>
                        <SelectItem
                            v-for="type in equipmentTypes"
                            :key="type.value"
                            :value="type.value"
                        >
                            {{ type.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Equipment</TableHead>
                            <TableHead>Muscle Group</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="exercise in filteredExercises" :key="exercise.id">
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
                                    {{ getEquipmentLabel(exercise.equipment_type) }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ exercise.muscle_group ?? '-' }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="`/exercises/${exercise.id}/edit`">
                                        <Button variant="ghost" size="sm">Edit</Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="confirmDelete(exercise)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="filteredExercises.length === 0">
                            <TableCell colspan="4" class="py-8 text-center text-muted-foreground">
                                No exercises found.
                                <Link href="/exercises/create" class="text-primary hover:underline">
                                    Add your first exercise
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </main>
    </div>

    <AlertDialog :open="!!exerciseToDelete" @update:open="exerciseToDelete = null">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Exercise</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete "{{ exerciseToDelete?.name }}"? This action
                    cannot be undone.
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
