<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

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
import { Textarea } from '@/components/ui/textarea';
import type { Activity, ActivityType, EquipmentType } from '@/types';

import { update, index } from '@/actions/App/Http/Controllers/ActivityController';

const props = defineProps<{
    activity: Activity;
    activityTypes: ActivityType[];
    equipmentTypes: EquipmentType[];
}>();

const form = useForm({
    name: props.activity.name,
    activity_type_id: props.activity.activity_type_id?.toString() ?? '',
    equipment_type: props.activity.equipment_type ?? '',
    muscle_group: props.activity.muscle_group ?? '',
    description: props.activity.description ?? '',
    instructions: props.activity.instructions ?? '',
});

function submit() {
    form.transform((data) => ({
        ...data,
        activity_type_id: data.activity_type_id || null,
        equipment_type: data.equipment_type || null,
    })).put(update.url(props.activity.id));
}
</script>

<template>
    <Head :title="`Edit ${activity.name}`" />
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
                    <CardTitle>Edit Activity</CardTitle>
                    <CardDescription>Update the activity details</CardDescription>
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
                            <Label for="activity_type_id">Category</Label>
                            <Select v-model="form.activity_type_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a category (optional)" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">No category</SelectItem>
                                    <SelectItem
                                        v-for="type in activityTypes"
                                        :key="type.id"
                                        :value="type.id.toString()"
                                    >
                                        {{ type.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.activity_type_id" class="text-sm text-destructive">
                                {{ form.errors.activity_type_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="equipment_type">Equipment Type</Label>
                            <Select v-model="form.equipment_type">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select equipment (optional)" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">No equipment</SelectItem>
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

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                Update Activity
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
