<script setup lang="ts">
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps<{
    checked: boolean;
    value?: any;
}>();

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:focus:ring-blue-600 dark:focus:ring-offset-slate-800"
    />
</template>
