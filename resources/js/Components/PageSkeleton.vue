<script setup lang="ts">
import Skeleton from '@/Components/Skeleton.vue';
</script>

<template>
    <div class="space-y-6" role="status" aria-label="Cargando contenido">
        <Skeleton class="h-7 w-56" />
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="n in 3" :key="n" class="card card-body space-y-3">
                <Skeleton class="h-4 w-24" />
                <Skeleton class="h-8 w-32" />
            </div>
        </div>
        <div class="card card-body space-y-3">
            <Skeleton class="h-4 w-1/3" />
            <Skeleton v-for="n in 5" :key="n" class="h-4 w-full" />
        </div>
        <span class="sr-only">Cargando…</span>
    </div>
</template>
