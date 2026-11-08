<script setup lang="ts">
import Icon from '@/Components/Icon.vue';
import { useToast } from '@/composables/useToast';

const { toasts, dismiss } = useToast();

const styles = {
    success: { icon: 'check-circle', classes: 'border-emerald-500 text-emerald-600 dark:text-emerald-400' },
    error: { icon: 'exclamation-circle', classes: 'border-rose-500 text-rose-600 dark:text-rose-400' },
    info: { icon: 'information-circle', classes: 'border-blue-500 text-blue-600 dark:text-blue-400' },
} as const;
</script>

<template>
    <div
        class="pointer-events-none fixed bottom-4 right-4 z-[60] flex w-full max-w-sm flex-col gap-2 px-4 sm:px-0"
        aria-live="polite"
    >
        <TransitionGroup
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex items-start gap-3 rounded-lg border-l-4 bg-white p-3 shadow-lg ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700"
                :class="styles[toast.type].classes"
                :role="toast.type === 'error' ? 'alert' : 'status'"
            >
                <Icon :name="styles[toast.type].icon" class="mt-0.5 h-5 w-5 shrink-0" />
                <p class="flex-1 text-sm text-slate-700 dark:text-slate-200">
                    {{ toast.message }}
                </p>
                <button
                    type="button"
                    class="rounded text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:text-slate-200"
                    aria-label="Cerrar notificación"
                    @click="dismiss(toast.id)"
                >
                    <Icon name="close" class="h-4 w-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
