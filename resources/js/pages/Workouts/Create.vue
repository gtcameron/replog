<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { FamilyMember, AppPageProps } from '@/types';

import { store, show } from '@/actions/App/Http/Controllers/WorkoutController';

const props = defineProps<{
    members: FamilyMember[];
    defaultUserId: number;
}>();

const page = usePage<AppPageProps>();
const activeWorkout = computed(() => page.props?.activeWorkout ?? null);

const form = useForm({
    user_id: props.defaultUserId.toString(),
    name: '',
});

function submit() {
    form.transform((data) => ({
        user_id: parseInt(data.user_id),
        name: data.name || null,
    })).post(store.url());
}
</script>

<template>
    <Head title="Start Workout" />
    <div class="min-h-screen bg-background" :class="{ 'pt-12': activeWorkout }">
        <nav class="border-b bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <Link href="/dashboard" class="text-lg font-semibold text-foreground">
                            RepLog
                        </Link>
                        <Link href="/dashboard" class="text-sm text-muted-foreground hover:text-foreground">
                            Dashboard
                        </Link>
                        <Link href="/workouts" class="text-sm text-muted-foreground hover:text-foreground">
                            Workouts
                        </Link>
                        <span class="text-sm font-medium text-foreground">Start</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Warning if there's an active workout -->
            <div v-if="activeWorkout" class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                <p class="text-sm text-yellow-800">
                    You already have an active workout.
                    <Link :href="show.url({ workout: activeWorkout.id })" class="font-medium underline">
                        Continue your current workout
                    </Link>
                    or end it before starting a new one.
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Start Workout</CardTitle>
                    <CardDescription>
                        Begin a new workout session. You can add activities as you go.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="user_id">Who is working out?</Label>
                            <Select v-model="form.user_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a family member" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="member in members"
                                        :key="member.id"
                                        :value="member.id.toString()"
                                    >
                                        {{ member.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.user_id" class="text-sm text-destructive">
                                {{ form.errors.user_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="name">Workout Name (optional)</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="e.g., Morning Strength, Leg Day"
                            />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing || !!activeWorkout">
                                Start Workout
                            </Button>
                            <Link href="/workouts">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
