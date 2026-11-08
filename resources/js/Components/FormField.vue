<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

defineProps<{
    label: string;
    for?: string;
    error?: string;
    hint?: string;
}>();
</script>

<template>
    <!-- Ley de Proximidad: etiqueta, control, ayuda y error forman un solo grupo -->
    <div class="space-y-1">
        <InputLabel :for="$props.for" :value="label" />
        <slot />
        <p v-if="hint && !error" class="text-xs text-slate-500 dark:text-slate-400">
            {{ hint }}
        </p>
        <InputError :message="error" />
    </div>
</template>
