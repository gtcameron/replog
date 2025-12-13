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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { FamilyMember } from '@/types';

import { create, edit, destroy } from '@/actions/App/Http/Controllers/FamilyMemberController';

defineProps<{
    members: FamilyMember[];
}>();

const memberToDelete = ref<FamilyMember | null>(null);

function confirmDelete(member: FamilyMember) {
    memberToDelete.value = member;
}

function deleteMember() {
    if (memberToDelete.value) {
        router.delete(destroy.url({ member: memberToDelete.value.id }));
        memberToDelete.value = null;
    }
}
</script>

<template>
    <Head title="Family Members" />
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
                            href="/family"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Family
                        </Link>
                        <span class="text-sm font-medium text-foreground">Members</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Family Members</CardTitle>
                            <CardDescription>Manage who can access and log activities</CardDescription>
                        </div>
                        <Link :href="create.url()">
                            <Button>Add Member</Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="member in members" :key="member.id">
                                <TableCell class="font-medium">{{ member.name }}</TableCell>
                                <TableCell>
                                    <span v-if="member.email">{{ member.email }}</span>
                                    <span v-else class="text-muted-foreground">—</span>
                                </TableCell>
                                <TableCell>
                                    <Badge v-if="member.can_login" variant="default">
                                        Can Login
                                    </Badge>
                                    <Badge v-else variant="secondary">
                                        Profile Only
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="edit.url({ member: member.id })">
                                            <Button variant="outline" size="sm">Edit</Button>
                                        </Link>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="confirmDelete(member)"
                                        >
                                            Remove
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="members.length === 0">
                                <TableCell colspan="4" class="text-center text-muted-foreground py-8">
                                    No family members yet. Add your first member to get started.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </main>
    </div>

    <AlertDialog :open="memberToDelete !== null" @update:open="memberToDelete = null">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Remove Family Member</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to remove {{ memberToDelete?.name }} from your family?
                    This action cannot be undone.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="deleteMember">Remove</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
