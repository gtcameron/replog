<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Family } from '@/types';

import { update } from '@/actions/App/Http/Controllers/FamilyController';
import { index as membersIndex } from '@/actions/App/Http/Controllers/FamilyMemberController';

const props = defineProps<{
    family: Family;
}>();

const form = useForm({
    name: props.family.name,
});

function submit() {
    form.put(update.url());
}
</script>

<template>
    <Head title="Family Settings" />
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
                            class="text-sm font-medium text-foreground"
                        >
                            Family
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Family Settings</CardTitle>
                        <CardDescription>Manage your family name and settings</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="name">Family Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. Smith Family"
                                    required
                                />
                                <p v-if="form.errors.name" class="text-sm text-destructive">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <Button type="submit" :disabled="form.processing">
                                Save Changes
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Family Members</CardTitle>
                        <CardDescription>Manage who can access and log activities</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Link :href="membersIndex.url()">
                            <Button variant="outline">Manage Members</Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </main>
    </div>
</template>
