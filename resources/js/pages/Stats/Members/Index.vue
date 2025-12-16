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
import type { MemberStats } from '@/types';

import { show } from '@/actions/App/Http/Controllers/MemberStatsController';
import { index as activitiesStatsIndex } from '@/actions/App/Http/Controllers/ActivityStatsController';

defineProps<{
    memberStats: MemberStats[];
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
    <Head title="Stats by Member" />
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
                    <h1 class="text-2xl font-semibold text-foreground">Stats by Member</h1>
                    <p class="mt-1 text-muted-foreground">
                        View progression and performance by family member
                    </p>
                </div>
                <Link :href="activitiesStatsIndex.url()">
                    <Button variant="outline">View by Activity</Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Family Members</CardTitle>
                    <CardDescription>Select a member to see their activity progression</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead class="text-center">Activities</TableHead>
                                <TableHead class="text-center">Total Sessions</TableHead>
                                <TableHead>Last Active</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="stat in memberStats" :key="stat.member.id">
                                <TableCell class="font-medium">
                                    {{ stat.member.name }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary">
                                        {{ stat.activitiesCount }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ stat.totalSessions }}
                                </TableCell>
                                <TableCell class="text-muted-foreground">
                                    {{ formatDate(stat.lastActive) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <Link :href="show.url({ member: stat.member.id })">
                                        <Button variant="outline" size="sm">
                                            View Progress
                                        </Button>
                                    </Link>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="memberStats.length === 0">
                                <TableCell colspan="5" class="py-8 text-center text-muted-foreground">
                                    No members found. Add family members to get started.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
