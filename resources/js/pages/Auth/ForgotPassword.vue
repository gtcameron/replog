<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

function submit() {
    form.post('/forgot-password');
}
</script>

<template>
    <Head title="Forgot Password" />
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-semibold">Forgot your password?</h1>
                <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    No problem. Enter your email and we'll send you a reset link.
                </p>
            </div>

            <div v-if="status" class="mb-4 rounded-md bg-green-50 p-4 text-sm font-medium text-green-600 dark:bg-green-900/20 dark:text-green-400">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-md border border-black bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black disabled:opacity-50 dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                >
                    Send reset link
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                <Link href="/login" class="font-medium text-[#1b1b18] hover:underline dark:text-[#EDEDEC]">Back to login</Link>
            </p>
        </div>
    </div>
</template>
