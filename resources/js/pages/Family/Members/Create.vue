<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import { store, index } from '@/actions/App/Http/Controllers/FamilyMemberController';

const form = useForm({
    name: '',
    email: '',
    password: '',
});

const showCredentials = computed(() => form.email.length > 0);

function submit() {
    form.transform((data) => ({
        ...data,
        email: data.email || null,
        password: data.password || null,
    })).post(store.url());
}
</script>

<template>
    <Head title="Add Family Member" />
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
            <Card>
                <CardHeader>
                    <CardTitle>Add Family Member</CardTitle>
                    <CardDescription>Add someone to your family who can have activities logged for them</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. John Smith"
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="border-t pt-6">
                            <h3 class="text-sm font-medium mb-4">Login Credentials (Optional)</h3>
                            <p class="text-sm text-muted-foreground mb-4">
                                If you want this member to be able to log in and manage their own activities,
                                provide an email and password. Otherwise, leave these blank.
                            </p>

                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="john@example.com"
                                    />
                                    <p v-if="form.errors.email" class="text-sm text-destructive">
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <div v-if="showCredentials" class="space-y-2">
                                    <Label for="password">Password</Label>
                                    <Input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        placeholder="At least 8 characters"
                                    />
                                    <p v-if="form.errors.password" class="text-sm text-destructive">
                                        {{ form.errors.password }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                Add Member
                            </Button>
                            <Link :href="index.url()">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
