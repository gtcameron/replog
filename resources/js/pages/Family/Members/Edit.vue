<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
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
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { FamilyMember } from '@/types';

import { update, index } from '@/actions/App/Http/Controllers/FamilyMemberController';
import { upgrade } from '@/routes/family/members';

const props = defineProps<{
    member: FamilyMember;
}>();

const form = useForm({
    name: props.member.name,
});

const upgradeForm = useForm({
    email: '',
    password: '',
});

const showUpgradeDialog = ref(false);

function submit() {
    form.put(update.url({ member: props.member.id }));
}

function submitUpgrade() {
    upgradeForm.post(upgrade.url({ member: props.member.id }), {
        onSuccess: () => {
            showUpgradeDialog.value = false;
        },
    });
}
</script>

<template>
    <Head title="Edit Family Member" />
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
                        <Link
                            :href="index.url()"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Members
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit Family Member</CardTitle>
                        <CardDescription>Update member details</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                />
                                <p v-if="form.errors.name" class="text-sm text-destructive">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button type="submit" :disabled="form.processing">
                                    Save Changes
                                </Button>
                                <Link :href="index.url()">
                                    <Button type="button" variant="outline">Cancel</Button>
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Card v-if="!member.can_login">
                    <CardHeader>
                        <CardTitle>Enable Login</CardTitle>
                        <CardDescription>
                            Allow this member to log in and manage their own activities
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button @click="showUpgradeDialog = true">
                            Add Login Credentials
                        </Button>
                    </CardContent>
                </Card>

                <Card v-else>
                    <CardHeader>
                        <CardTitle>Login Status</CardTitle>
                        <CardDescription>This member can log in with their email</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            Email: {{ member.email }}
                        </p>
                    </CardContent>
                </Card>
            </div>
        </main>
    </div>

    <AlertDialog v-model:open="showUpgradeDialog">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Add Login Credentials</AlertDialogTitle>
                <AlertDialogDescription>
                    This will allow {{ member.name }} to log in and manage activities.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <form @submit.prevent="submitUpgrade" class="space-y-4 py-4">
                <div class="space-y-2">
                    <Label for="upgrade-email">Email</Label>
                    <Input
                        id="upgrade-email"
                        v-model="upgradeForm.email"
                        type="email"
                        required
                    />
                    <p v-if="upgradeForm.errors.email" class="text-sm text-destructive">
                        {{ upgradeForm.errors.email }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="upgrade-password">Password</Label>
                    <Input
                        id="upgrade-password"
                        v-model="upgradeForm.password"
                        type="password"
                        placeholder="At least 8 characters"
                        required
                    />
                    <p v-if="upgradeForm.errors.password" class="text-sm text-destructive">
                        {{ upgradeForm.errors.password }}
                    </p>
                </div>
            </form>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="submitUpgrade" :disabled="upgradeForm.processing">
                    Add Credentials
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
