<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Register" />
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-semibold">Create an account</h1>
                <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">Get started with your workout tracking</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium">Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
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
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                    />
                    <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ form.errors.password_confirmation }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-md border border-black bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black disabled:opacity-50 dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                >
                    Create account
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                Already have an account?
                <Link href="/login" class="font-medium text-[#1b1b18] hover:underline dark:text-[#EDEDEC]">Sign in</Link>
            </p>
        </div>
    </div>
</template>
