<script setup lang="ts">
import Dropdown from '@/Components/Dropdown.vue';
import Icon from '@/Components/Icon.vue';
import type { PageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<PageProps>();
const tenancy = computed(() => page.props.tenancy);

const branchLabel = computed(() => {
    if (tenancy.value.all_branches) return 'Todas las sucursales';
    const count = tenancy.value.branches.length;
    return count === 1 ? tenancy.value.branches[0].name : `${count} sucursales`;
});

function switchTo(id: number): void {
    if (id === tenancy.value.company?.id) return;
    router.post(route('company.switch', id));
}
</script>

<template>
    <Dropdown v-if="tenancy.company" align="left" content-classes="py-1 bg-white dark:bg-slate-800">
        <template #trigger>
            <button
                type="button"
                class="flex items-center gap-2 rounded-md px-2 py-1.5 text-left transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-slate-800"
                :aria-label="`Empresa activa: ${tenancy.company.name}`"
            >
                <Icon name="building" class="h-5 w-5 text-slate-500 dark:text-slate-400" />
                <span class="hidden min-w-0 sm:block">
                    <span class="block max-w-[11rem] truncate text-sm font-medium text-slate-900 dark:text-slate-100">
                        {{ tenancy.company.name }}
                    </span>
                    <span class="block max-w-[11rem] truncate text-xs text-slate-500 dark:text-slate-400">
                        {{ branchLabel }}
                    </span>
                </span>
                <Icon v-if="tenancy.companies.length > 1" name="chevron-down" class="h-4 w-4 text-slate-400" />
            </button>
        </template>

        <template #content>
            <p class="px-4 py-2 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Empresa
            </p>
            <button
                v-for="company in tenancy.companies"
                :key="company.id"
                type="button"
                class="flex w-full items-center justify-between px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100 focus:bg-slate-100 focus:outline-none dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
                @click="switchTo(company.id)"
            >
                {{ company.name }}
                <Icon v-if="company.id === tenancy.company.id" name="check-circle" class="h-4 w-4 text-emerald-500" />
            </button>
        </template>
    </Dropdown>
</template>
