<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Login" />
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-semibold">Welcome back</h1>
                <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">Sign in to your account</p>
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

                <div>
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-[#e3e3e0] text-[#1b1b18] focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615]"
                        />
                        <span class="ml-2 text-sm">Remember me</span>
                    </label>
                    <Link href="/forgot-password" class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
                        Forgot password?
                    </Link>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-md border border-black bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black disabled:opacity-50 dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                >
                    Sign in
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                Don't have an account?
                <Link href="/register" class="font-medium text-[#1b1b18] hover:underline dark:text-[#EDEDEC]">Sign up</Link>
            </p>
        </div>
    </div>
</template>
