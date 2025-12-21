<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { Invite } from '@/types';

import { index, store, destroy } from '@/actions/App/Http/Controllers/Admin/InviteController';

defineProps<{
    invites: Invite[];
}>();

const form = useForm({
    email: '',
});

const inviteToDelete = ref<Invite | null>(null);

function submitInvite() {
    form.post(store(), {
        onSuccess: () => form.reset(),
    });
}

function confirmDelete(invite: Invite) {
    inviteToDelete.value = invite;
}

function deleteInvite() {
    if (inviteToDelete.value) {
        router.delete(destroy.url(inviteToDelete.value.id), {
            onSuccess: () => {
                inviteToDelete.value = null;
            },
        });
    }
}

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Manage Invites" />
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
                            Admin: Invites
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-foreground">Manage Invites</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Add email addresses that are allowed to register
                </p>
            </div>

            <div class="mb-8 rounded-lg border bg-card p-6">
                <form @submit.prevent="submitInvite" class="flex items-end gap-4">
                    <div class="flex-1">
                        <Label for="email">Email Address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="user@example.com"
                            class="mt-1"
                            required
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-destructive">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <Button type="submit" :disabled="form.processing">
                        Add Invite
                    </Button>
                </form>
            </div>

            <div class="rounded-lg border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Email</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="invite in invites" :key="invite.id">
                            <TableCell class="font-medium">
                                {{ invite.email }}
                            </TableCell>
                            <TableCell>
                                <Badge v-if="invite.claimed_at" variant="secondary">
                                    Claimed {{ formatDate(invite.claimed_at) }}
                                </Badge>
                                <Badge v-else variant="default">
                                    Pending
                                </Badge>
                            </TableCell>
                            <TableCell>
                                {{ formatDate(invite.created_at) }}
                            </TableCell>
                            <TableCell>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive hover:text-destructive"
                                    @click="confirmDelete(invite)"
                                >
                                    Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="invites.length === 0">
                            <TableCell colspan="4" class="py-8 text-center text-muted-foreground">
                                No invites yet. Add an email address above to allow registration.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </main>
    </div>

    <AlertDialog :open="!!inviteToDelete" @update:open="inviteToDelete = null">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Invite</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete the invite for "{{ inviteToDelete?.email }}"?
                    <span v-if="!inviteToDelete?.claimed_at">
                        This email will no longer be able to register.
                    </span>
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    @click="deleteInvite"
                >
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
