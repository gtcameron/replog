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
import type { Activity } from '@/types';

import { index, edit, destroy } from '@/actions/App/Http/Controllers/ActivityController';

const props = defineProps<{
    activity: Activity;
}>();

const showDeleteDialog = ref(false);

function deleteActivity() {
    router.delete(destroy.url(props.activity.id));
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
    <Head :title="activity.name" />
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
                            Activities
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
                            <CardTitle class="text-2xl">{{ activity.name }}</CardTitle>
                            <CardDescription class="mt-2 flex flex-wrap items-center gap-2">
                                <Badge v-if="activity.equipment_type" variant="secondary">
                                    {{ equipmentLabels[activity.equipment_type] ?? activity.equipment_type }}
                                </Badge>
                                <span v-if="activity.muscle_group">{{ activity.muscle_group }}</span>
                            </CardDescription>
                        </div>
                        <div class="flex items-center gap-2">
                            <Link :href="edit.url(activity.id)">
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
                    <div v-if="activity.description">
                        <h3 class="text-sm font-medium text-muted-foreground">Description</h3>
                        <p class="mt-1 text-foreground">{{ activity.description }}</p>
                    </div>

                    <div v-if="activity.instructions">
                        <h3 class="text-sm font-medium text-muted-foreground">Instructions</h3>
                        <p class="mt-1 whitespace-pre-wrap text-foreground">
                            {{ activity.instructions }}
                        </p>
                    </div>

                    <div
                        v-if="!activity.description && !activity.instructions"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No additional details provided for this activity.
                    </div>
                </CardContent>
            </Card>

            <div class="mt-6">
                <Link :href="index.url()">
                    <Button variant="outline">Back to Activities</Button>
                </Link>
            </div>
        </main>
    </div>

    <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = false">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Activity</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete "{{ activity.name }}"? This action cannot be
                    undone.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    @click="deleteActivity"
                >
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
