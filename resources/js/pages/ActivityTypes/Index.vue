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
import type { ActivityType } from '@/types';

import { index, create, edit, destroy } from '@/actions/App/Http/Controllers/ActivityTypeController';
import { index as activitiesIndex } from '@/actions/App/Http/Controllers/ActivityController';

defineProps<{
    activityTypes: ActivityType[];
}>();

const typeToDelete = ref<ActivityType | null>(null);

function confirmDelete(type: ActivityType) {
    typeToDelete.value = type;
}

function deleteType() {
    if (typeToDelete.value) {
        router.delete(destroy.url(typeToDelete.value.id), {
            onSuccess: () => {
                typeToDelete.value = null;
            },
        });
    }
}
</script>

<template>
    <Head title="Categories" />
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
                            :href="activitiesIndex.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Activities
                        </Link>
                        <Link
                            :href="index.url()"
                            class="text-sm font-medium text-foreground"
                        >
                            Categories
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Categories</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage your activity categories
                    </p>
                </div>
                <Link :href="create.url()">
                    <Button>Add Category</Button>
                </Link>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Color</TableHead>
                            <TableHead>Activities</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="type in activityTypes" :key="type.id">
                            <TableCell class="font-medium">
                                <Badge :style="{ backgroundColor: type.color, color: 'white' }">
                                    {{ type.name }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ type.description ?? '-' }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-4 w-4 rounded border"
                                        :style="{ backgroundColor: type.color }"
                                    />
                                    <span class="text-sm text-muted-foreground">{{ type.color }}</span>
                                </div>
                            </TableCell>
                            <TableCell>{{ type.activities_count ?? 0 }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="edit.url(type.id)">
                                        <Button variant="ghost" size="sm">Edit</Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="confirmDelete(type)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="activityTypes.length === 0">
                            <TableCell colspan="5" class="py-8 text-center text-muted-foreground">
                                No categories found.
                                <Link :href="create.url()" class="text-primary hover:underline">
                                    Add your first category
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </main>
    </div>

    <AlertDialog :open="!!typeToDelete" @update:open="typeToDelete = null">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Category</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete "{{ typeToDelete?.name }}"? Activities using
                    this category will have their category set to none.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    @click="deleteType"
                >
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
