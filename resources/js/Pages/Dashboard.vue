<script setup lang="ts">
import Card from '@/Components/Card.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<PageProps>();
const tenancy = computed(() => page.props.tenancy);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900 dark:text-slate-100">
                Dashboard
            </h2>
        </template>

        <div v-if="tenancy.company" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Card title="Empresa activa">
                <p class="text-lg font-semibold">{{ tenancy.company.name }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Moneda base {{ tenancy.company.base_currency }}
                </p>
            </Card>

            <Card title="Tipo de cambio">
                <p class="text-lg font-semibold">
                    {{ Number(tenancy.company.exchange_rate).toFixed(2) }} {{ tenancy.company.base_currency }}
                </p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    por 1 {{ tenancy.company.foreign_currency }}
                </p>
            </Card>

            <Card title="Sucursales con acceso">
                <p v-if="tenancy.all_branches" class="text-sm">Todas las sucursales de la empresa</p>
                <ul v-else class="space-y-1 text-sm">
                    <li v-for="branch in tenancy.branches" :key="branch.id">
                        {{ branch.name }}
                        <span class="text-slate-500 dark:text-slate-400">({{ branch.code }})</span>
                    </li>
                </ul>
            </Card>
        </div>

        <Card v-else title="Sin empresa asignada">
            <p class="text-sm text-slate-600 dark:text-slate-300">
                Tu usuario no pertenece a ninguna empresa activa. Contacta a un administrador.
            </p>
        </Card>
    </AuthenticatedLayout>
</template>
