<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import CommandPalette from '@/Components/CommandPalette.vue';
import CompanySwitcher from '@/Components/CompanySwitcher.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Icon from '@/Components/Icon.vue';
import PageSkeleton from '@/Components/PageSkeleton.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import { navigation } from '@/config/navigation';
import { useSidebar } from '@/composables/useSidebar';
import { useToast } from '@/composables/useToast';
import type { PageProps } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const page = usePage<PageProps>();
const user = computed(() => page.props.auth.user);
const permissions = computed(() => page.props.auth.permissions ?? []);

const { collapsed, toggle } = useSidebar();
const mobileOpen = ref(false);
const paletteOpen = ref(false);

// Navegación filtrada por permisos (compartida con el buscador Cmd+K)
const groups = computed(() =>
    navigation
        .map((group) => ({
            ...group,
            items: group.items.filter(
                (item) => !item.permission || permissions.value.includes(item.permission),
            ),
        }))
        .filter((group) => group.items.length > 0),
);
const allItems = computed(() => groups.value.flatMap((group) => group.items));

// Atajo global Cmd/Ctrl + K
function onKeydown(event: KeyboardEvent): void {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        paletteOpen.value = !paletteOpen.value;
    }
}
onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => document.removeEventListener('keydown', onKeydown));

const isMac = computed(
    () => typeof navigator !== 'undefined' && /Mac|iPhone|iPad/.test(navigator.platform),
);

// Mensajes flash del backend -> toasts no invasivos
const toast = useToast();
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) toast.success(flash.success);
        if (flash?.error) toast.error(flash.error);
        if (flash?.info) toast.info(flash.info);
    },
    { immediate: true },
);

// Skeleton durante la navegación (con retardo para evitar parpadeos)
const navigating = ref(false);
let timer: ReturnType<typeof setTimeout> | undefined;
const offStart = router.on('start', (event) => {
    if (event.detail.visit.only.length > 0) return; // recargas parciales: sin skeleton
    timer = setTimeout(() => (navigating.value = true), 150);
});
const offFinish = router.on('finish', () => {
    clearTimeout(timer);
    navigating.value = false;
    mobileOpen.value = false;
});
onUnmounted(() => {
    offStart();
    offFinish();
    clearTimeout(timer);
});
</script>

<template>
    <div class="min-h-screen">
        <!-- Overlay móvil -->
        <div
            v-if="mobileOpen"
            class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"
            @click="mobileOpen = false"
        />

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-200 bg-white transition-all duration-200 dark:border-slate-800 dark:bg-slate-900"
            :class="[
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                collapsed ? 'lg:w-16' : 'lg:w-64',
            ]"
            aria-label="Navegación principal"
        >
            <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-200 px-4 dark:border-slate-800">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <ApplicationLogo class="h-8 w-8 shrink-0 fill-current text-blue-600 dark:text-blue-400" />
                    <span class="text-base font-semibold" :class="collapsed ? 'lg:hidden' : ''">ERP</span>
                </Link>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
                <div v-for="group in groups" :key="group.label">
                    <p
                        class="mb-2 px-3 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        :class="collapsed ? 'lg:hidden' : ''"
                    >
                        {{ group.label }}
                    </p>
                    <ul class="space-y-1">
                        <li v-for="item in group.items" :key="item.routeName">
                            <Link
                                :href="route(item.routeName)"
                                :title="item.label"
                                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="
                                    route().current(item.routeName)
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'
                                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                                "
                            >
                                <Icon :name="item.icon" class="h-5 w-5 shrink-0" />
                                <span :class="collapsed ? 'lg:hidden' : ''">{{ item.label }}</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </nav>

            <button
                type="button"
                class="hidden h-12 shrink-0 items-center justify-center border-t border-slate-200 text-slate-500 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:flex dark:border-slate-800 dark:text-slate-400 dark:hover:bg-slate-800"
                :aria-label="collapsed ? 'Expandir menú' : 'Contraer menú'"
                @click="toggle"
            >
                <Icon name="chevron-left" class="h-5 w-5 transition-transform" :class="collapsed ? 'rotate-180' : ''" />
            </button>
        </aside>

        <!-- Contenido -->
        <div class="transition-all duration-200" :class="collapsed ? 'lg:pl-16' : 'lg:pl-64'">
            <!-- Topbar -->
            <header
                class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 dark:border-slate-800 dark:bg-slate-900/90"
            >
                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden dark:text-slate-400 dark:hover:bg-slate-800"
                    aria-label="Abrir menú"
                    @click="mobileOpen = true"
                >
                    <Icon name="menu" class="h-5 w-5" />
                </button>

                <CompanySwitcher />

                <button
                    type="button"
                    class="ml-auto flex h-9 w-full max-w-xs items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 text-sm text-slate-500 transition hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:ml-4 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:border-slate-600"
                    aria-label="Abrir buscador global"
                    @click="paletteOpen = true"
                >
                    <Icon name="search" class="h-4 w-4" />
                    <span class="flex-1 text-left">Buscar…</span>
                    <kbd class="hidden rounded border border-slate-300 px-1.5 text-[10px] sm:inline dark:border-slate-600">
                        {{ isMac ? '⌘' : 'Ctrl' }} K
                    </kbd>
                </button>

                <ThemeToggle />

                <Dropdown align="right">
                    <template #trigger>
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-xs font-semibold text-white" aria-hidden="true">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </span>
                            <span class="hidden max-w-[8rem] truncate md:block">{{ user.name }}</span>
                            <Icon name="chevron-down" class="h-4 w-4 text-slate-400" />
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">
                            Cerrar sesión
                        </DropdownLink>
                    </template>
                </Dropdown>
            </header>

            <header
                v-if="$slots.header"
                class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8 dark:border-slate-800 dark:bg-slate-900"
            >
                <slot name="header" />
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <!-- v-show: conserva el estado de la página mientras se muestra el skeleton -->
                <div v-show="!navigating">
                    <slot />
                </div>
                <PageSkeleton v-show="navigating" />
            </main>
        </div>

        <CommandPalette :open="paletteOpen" :items="allItems" @close="paletteOpen = false" />
        <ToastContainer />
    </div>
</template>
