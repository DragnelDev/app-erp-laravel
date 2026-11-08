import { computed, ref } from 'vue';

export type ThemeMode = 'light' | 'dark' | 'system';

const STORAGE_KEY = 'theme';

const mode = ref<ThemeMode>('system');
const systemDark = ref(false);
let initialized = false;

function readStored(): ThemeMode {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        return value === 'light' || value === 'dark' || value === 'system'
            ? value
            : 'system';
    } catch {
        return 'system';
    }
}

function apply(): void {
    const dark =
        mode.value === 'dark' || (mode.value === 'system' && systemDark.value);
    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
}

function init(): void {
    if (initialized || typeof window === 'undefined') return;
    initialized = true;

    mode.value = readStored();

    const query = window.matchMedia('(prefers-color-scheme: dark)');
    systemDark.value = query.matches;
    query.addEventListener('change', (event) => {
        systemDark.value = event.matches;
        apply();
    });

    apply();
}

export function useTheme() {
    init();

    function setMode(next: ThemeMode): void {
        mode.value = next;
        try {
            localStorage.setItem(STORAGE_KEY, next);
        } catch {
            // almacenamiento no disponible: el tema solo dura la sesión
        }
        apply();
    }

    function cycle(): void {
        const order: ThemeMode[] = ['light', 'dark', 'system'];
        setMode(order[(order.indexOf(mode.value) + 1) % order.length]);
    }

    const isDark = computed(
        () =>
            mode.value === 'dark' ||
            (mode.value === 'system' && systemDark.value),
    );

    return { mode, isDark, setMode, cycle };
}
