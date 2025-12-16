<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

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
import type { ActivityStats } from '@/types';

import { show } from '@/actions/App/Http/Controllers/ActivityStatsController';
import { index as memberStatsIndex } from '@/actions/App/Http/Controllers/MemberStatsController';

defineProps<{
    activityStats: ActivityStats[];
}>();

function formatDate(dateString: string | null) {
    if (!dateString) return 'Never';
    return new Date(dateString).toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Stats by Activity" />
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
                        <span class="text-sm font-medium text-foreground">Stats</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Stats by Activity</h1>
                    <p class="mt-1 text-muted-foreground">
                        View leaderboards and compare performance across members
                    </p>
                </div>
                <Link :href="memberStatsIndex.url()">
                    <Button variant="outline">View by Member</Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Activities</CardTitle>
                    <CardDescription>Select an activity to see leaderboards and comparisons</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Activity</TableHead>
                                <TableHead>Equipment</TableHead>
                                <TableHead class="text-center">Participants</TableHead>
                                <TableHead class="text-center">Total Logs</TableHead>
                                <TableHead>Last Performed</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="stat in activityStats" :key="stat.activity.id">
                                <TableCell class="font-medium">
                                    {{ stat.activity.name }}
                                </TableCell>
                                <TableCell>
                                    <Badge v-if="stat.activity.equipment_type" variant="secondary">
                                        {{ stat.activity.equipment_type }}
                                    </Badge>
                                    <span v-else class="text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary">
                                        {{ stat.participantsCount }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ stat.totalLogs }}
                                </TableCell>
                                <TableCell class="text-muted-foreground">
                                    {{ formatDate(stat.lastPerformed) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <Link :href="show.url({ activity: stat.activity.id })">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            :disabled="stat.totalLogs === 0"
                                        >
                                            View Stats
                                        </Button>
                                    </Link>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="activityStats.length === 0">
                                <TableCell colspan="6" class="py-8 text-center text-muted-foreground">
                                    No activities found. Create activities to get started.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
