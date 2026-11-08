<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-slate-50 pt-6 sm:justify-center sm:pt-0 dark:bg-slate-950"
    >
        <div class="absolute right-4 top-4">
            <ThemeToggle />
        </div>

        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-blue-600 dark:text-blue-400" />
            </Link>
        </div>

        <div class="card mt-6 w-full overflow-hidden px-6 py-4 sm:max-w-md">
            <slot />
        </div>

        <ToastContainer />
    </div>
</template>
