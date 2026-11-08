<script setup lang="ts">
import { computed } from 'vue';

export type DocumentStatus = 'draft' | 'invoiced' | 'cancelled';

const props = defineProps<{ status: DocumentStatus }>();

// Ley de Similitud: mismo formato para todos los estados, solo cambia color + texto + punto.
const config: Record<DocumentStatus, { label: string; classes: string; dot: string }> = {
    draft: {
        label: 'Borrador',
        classes: 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-400/20',
        dot: 'bg-slate-500',
    },
    invoiced: {
        label: 'Facturado',
        classes: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/20',
        dot: 'bg-emerald-500',
    },
    cancelled: {
        label: 'Anulado',
        classes: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-400/20',
        dot: 'bg-rose-500',
    },
};

const current = computed(() => config[props.status]);
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
        :class="current.classes"
    >
        <span class="h-1.5 w-1.5 rounded-full" :class="current.dot" aria-hidden="true" />
        {{ current.label }}
    </span>
</template>
