<script setup lang="ts">
import Icon from '@/Components/Icon.vue';
import { useTheme } from '@/composables/useTheme';
import { computed } from 'vue';

const { mode, cycle } = useTheme();

const labels = {
    light: 'Tema claro',
    dark: 'Tema oscuro',
    system: 'Tema del sistema',
} as const;

const label = computed(() => labels[mode.value]);
</script>

<template>
    <button
        type="button"
        class="inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
        :title="`${label} (clic para cambiar)`"
        :aria-label="`${label}. Clic para cambiar`"
        @click="cycle"
    >
        <Icon
            :name="mode === 'light' ? 'sun' : mode === 'dark' ? 'moon' : 'system'"
            class="h-5 w-5"
        />
    </button>
</template>
