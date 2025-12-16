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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { Activity, EquipmentType } from '@/types';

import { index, destroy } from '@/actions/App/Http/Controllers/ActivityController';

const props = defineProps<{
    activities: Activity[];
    equipmentTypes: EquipmentType[];
}>();

const activityToDelete = ref<Activity | null>(null);

function getEquipmentLabel(value: string | null): string {
    if (!value) return '-';
    return props.equipmentTypes.find((t) => t.value === value)?.label ?? value;
}

function confirmDelete(activity: Activity) {
    activityToDelete.value = activity;
}

function deleteActivity() {
    if (activityToDelete.value) {
        router.delete(destroy.url(activityToDelete.value.id), {
            onSuccess: () => {
                activityToDelete.value = null;
            },
        });
    }
}
</script>

<template>
    <Head title="Activities" />
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
                            class="text-sm font-medium text-foreground"
                        >
                            Activities
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Activities</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage your activity library
                    </p>
                </div>
                <Link href="/activities/create">
                    <Button>Add Activity</Button>
                </Link>
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
                        <TableRow v-for="activity in activities" :key="activity.id">
                            <TableCell class="font-medium">
                                <Link
                                    :href="`/activities/${activity.id}`"
                                    class="hover:underline"
                                >
                                    {{ activity.name }}
                                </Link>
                            </TableCell>
                            <TableCell>
                                <Badge v-if="activity.equipment_type" variant="secondary">
                                    {{ getEquipmentLabel(activity.equipment_type) }}
                                </Badge>
                                <span v-else class="text-muted-foreground">-</span>
                            </TableCell>
                            <TableCell>{{ activity.muscle_group ?? '-' }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="`/activities/${activity.id}/edit`">
                                        <Button variant="ghost" size="sm">Edit</Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="confirmDelete(activity)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="activities.length === 0">
                            <TableCell colspan="4" class="py-8 text-center text-muted-foreground">
                                No activities found.
                                <Link href="/activities/create" class="text-primary hover:underline">
                                    Add your first activity
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </main>
    </div>

    <AlertDialog :open="!!activityToDelete" @update:open="activityToDelete = null">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Activity</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete "{{ activityToDelete?.name }}"? This action
                    cannot be undone.
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
