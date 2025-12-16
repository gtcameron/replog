<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { EquipmentType } from '@/types';

import { store, index } from '@/actions/App/Http/Controllers/ActivityController';

defineProps<{
    equipmentTypes: EquipmentType[];
}>();

const form = useForm({
    name: '',
    equipment_type: '',
    muscle_group: '',
    description: '',
    instructions: '',
    tracks_reps: false,
    tracks_weight: false,
    tracks_duration: false,
    tracks_distance: false,
});

function submit() {
    form.transform((data) => ({
        ...data,
        equipment_type: data.equipment_type && data.equipment_type !== '__none__' ? data.equipment_type : null,
    })).post(store.url());
}
</script>

<template>
    <Head title="Create Activity" />
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
                    <CardTitle>Create Activity</CardTitle>
                    <CardDescription>Add a new activity to your library</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Bench Press, Free Throws, Morning Meditation"
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="equipment_type">Equipment Type</Label>
                            <Select v-model="form.equipment_type">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select equipment (optional)" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="__none__">No equipment</SelectItem>
                                    <SelectItem
                                        v-for="type in equipmentTypes"
                                        :key="type.value"
                                        :value="type.value"
                                    >
                                        {{ type.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.equipment_type" class="text-sm text-destructive">
                                {{ form.errors.equipment_type }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="muscle_group">Muscle Group / Target Area</Label>
                            <Input
                                id="muscle_group"
                                v-model="form.muscle_group"
                                type="text"
                                placeholder="e.g. Chest, Back, Legs, Ball Handling"
                            />
                            <p v-if="form.errors.muscle_group" class="text-sm text-destructive">
                                {{ form.errors.muscle_group }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Brief description of the activity"
                                rows="3"
                            />
                            <p v-if="form.errors.description" class="text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="instructions">Instructions</Label>
                            <Textarea
                                id="instructions"
                                v-model="form.instructions"
                                placeholder="Step-by-step instructions for performing the activity"
                                rows="5"
                            />
                            <p v-if="form.errors.instructions" class="text-sm text-destructive">
                                {{ form.errors.instructions }}
                            </p>
                        </div>

                        <div class="border-t pt-6">
                            <h3 class="text-sm font-medium mb-4">Tracked Metrics</h3>
                            <p class="text-sm text-muted-foreground mb-4">
                                Select which metrics to track when logging this activity.
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        id="tracks_reps"
                                        :checked="form.tracks_reps"
                                        @update:checked="form.tracks_reps = $event"
                                    />
                                    <Label for="tracks_reps" class="font-normal">Reps</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        id="tracks_weight"
                                        :checked="form.tracks_weight"
                                        @update:checked="form.tracks_weight = $event"
                                    />
                                    <Label for="tracks_weight" class="font-normal">Weight</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        id="tracks_duration"
                                        :checked="form.tracks_duration"
                                        @update:checked="form.tracks_duration = $event"
                                    />
                                    <Label for="tracks_duration" class="font-normal">Duration</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        id="tracks_distance"
                                        :checked="form.tracks_distance"
                                        @update:checked="form.tracks_distance = $event"
                                    />
                                    <Label for="tracks_distance" class="font-normal">Distance</Label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                Create Activity
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
