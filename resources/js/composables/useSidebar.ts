import { ref, watch } from 'vue';

const STORAGE_KEY = 'sidebar-collapsed';

function readStored(): boolean {
    try {
        return localStorage.getItem(STORAGE_KEY) === '1';
    } catch {
        return false;
    }
}

const collapsed = ref(readStored());

watch(collapsed, (value) => {
    try {
        localStorage.setItem(STORAGE_KEY, value ? '1' : '0');
    } catch {
        // sin persistencia
    }
});

export function useSidebar() {
    return {
        collapsed,
        toggle: () => (collapsed.value = !collapsed.value),
    };
}
