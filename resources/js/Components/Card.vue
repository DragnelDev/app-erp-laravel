<script setup lang="ts">
defineProps<{ title?: string }>();
</script>

<template>
    <section class="card">
        <header v-if="title || $slots.header" class="card-header">
            <slot name="header">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                    {{ title }}
                </h3>
            </slot>
        </header>
        <div class="card-body">
            <slot />
        </div>
    </section>
</template>
