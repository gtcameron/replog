<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    auth?: {
        user: {
            id: number;
            name: string;
            email: string;
        } | null;
    };
}>();
</script>

<template>
    <Head title="RepLog - Workout Tracker" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <nav class="flex items-center justify-between p-6">
            <span class="text-xl font-semibold">RepLog</span>
            <div class="flex items-center gap-4">
                <template v-if="auth?.user">
                    <Link href="/dashboard" class="text-sm font-medium text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
                        Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link href="/login" class="text-sm font-medium text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
                        Login
                    </Link>
                    <Link href="/register" class="rounded-md border border-black bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white">
                        Get Started
                    </Link>
                </template>
            </div>
        </nav>

        <main class="flex flex-1 flex-col items-center justify-center px-6 text-center">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">Track your workouts</h1>
            <p class="mt-4 max-w-md text-lg text-[#706f6c] dark:text-[#A1A09A]">
                Simple workout logging to help you stay consistent and see your progress over time.
            </p>
            <div class="mt-8 flex gap-4">
                <template v-if="auth?.user">
                    <Link href="/dashboard" class="rounded-md border border-black bg-[#1b1b18] px-6 py-3 text-sm font-medium text-white hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white">
                        Go to Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link href="/register" class="rounded-md border border-black bg-[#1b1b18] px-6 py-3 text-sm font-medium text-white hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white">
                        Start Tracking
                    </Link>
                    <Link href="/login" class="rounded-md border border-[#e3e3e0] bg-white px-6 py-3 text-sm font-medium text-[#1b1b18] hover:bg-[#f5f5f4] dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:hover:bg-[#1f1f1e]">
                        Sign In
                    </Link>
                </template>
            </div>
        </main>
    </div>
</template>
