<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
    };
}>();

function logout() {
    router.post('/logout');
}
</script>

<template>
    <Head title="Dashboard" />
    <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a]">
        <nav class="border-b border-[#e3e3e0] bg-white dark:border-[#3E3E3A] dark:bg-[#161615]">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">RepLog</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ user.name }}</span>
                        <button
                            @click="logout"
                            class="rounded-md px-3 py-2 text-sm font-medium text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <h1 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Dashboard</h1>
                <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">
                    Welcome back, {{ user.name }}! Your workout tracking dashboard will be here.
                </p>
            </div>
        </main>
    </div>
</template>
