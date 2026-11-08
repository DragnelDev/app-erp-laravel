<script setup lang="ts">
import Icon from '@/Components/Icon.vue';
import type { NavItem } from '@/config/navigation';
import { router } from '@inertiajs/vue3';
import { computed, nextTick, ref, watch } from 'vue';

const props = defineProps<{ open: boolean; items: NavItem[] }>();
const emit = defineEmits<{ close: [] }>();

const query = ref('');
const active = ref(0);
const input = ref<HTMLInputElement | null>(null);

function normalize(value: string): string {
    return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

const results = computed(() => {
    const term = normalize(query.value.trim());
    if (!term) return props.items;

    return props.items.filter((item) =>
        normalize([item.label, ...(item.keywords ?? [])].join(' ')).includes(term),
    );
});

watch(
    () => props.open,
    async (isOpen) => {
        if (!isOpen) return;
        query.value = '';
        active.value = 0;
        await nextTick();
        input.value?.focus();
    },
);

watch(results, () => (active.value = 0));

function go(item: NavItem): void {
    emit('close');
    router.visit(route(item.routeName));
}

function onKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        emit('close');
    } else if (event.key === 'ArrowDown') {
        event.preventDefault();
        active.value = Math.min(active.value + 1, results.value.length - 1);
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        active.value = Math.max(active.value - 1, 0);
    } else if (event.key === 'Enter' && results.value[active.value]) {
        event.preventDefault();
        go(results.value[active.value]);
    }
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-start justify-center px-4 pt-[15vh]"
            role="dialog"
            aria-modal="true"
            aria-label="Buscador global"
            @keydown="onKeydown"
        >
            <div
                class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                @click="emit('close')"
            />

            <div
                class="relative w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700"
            >
                <div class="flex items-center gap-3 border-b border-slate-200 px-4 dark:border-slate-700">
                    <Icon name="search" class="h-5 w-5 text-slate-400" />
                    <input
                        ref="input"
                        v-model="query"
                        type="text"
                        class="h-12 flex-1 border-0 bg-transparent p-0 text-sm text-slate-900 placeholder-slate-400 focus:ring-0 dark:text-slate-100"
                        placeholder="Buscar páginas y acciones…"
                        aria-label="Buscar"
                    />
                    <kbd class="rounded border border-slate-300 px-1.5 py-0.5 text-[10px] text-slate-500 dark:border-slate-600 dark:text-slate-400">Esc</kbd>
                </div>

                <ul class="max-h-72 overflow-y-auto p-2" role="listbox">
                    <li v-if="results.length === 0" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                        Sin resultados
                    </li>
                    <li
                        v-for="(item, index) in results"
                        :key="item.routeName"
                        role="option"
                        :aria-selected="index === active"
                        class="flex cursor-pointer items-center gap-3 rounded-md px-3 py-2 text-sm"
                        :class="
                            index === active
                                ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'
                                : 'text-slate-700 dark:text-slate-300'
                        "
                        @mouseenter="active = index"
                        @click="go(item)"
                    >
                        <Icon :name="item.icon" class="h-5 w-5" />
                        {{ item.label }}
                    </li>
                </ul>
            </div>
        </div>
    </Teleport>
</template>
