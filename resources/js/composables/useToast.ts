import { ref } from 'vue';

export type ToastType = 'success' | 'error' | 'info';

export interface Toast {
    id: number;
    type: ToastType;
    message: string;
}

const toasts = ref<Toast[]>([]);
let nextId = 1;

export function useToast() {
    function dismiss(id: number): void {
        toasts.value = toasts.value.filter((toast) => toast.id !== id);
    }

    function push(message: string, type: ToastType = 'info', timeout = 4500): void {
        const id = nextId++;
        toasts.value = [...toasts.value, { id, type, message }];

        if (timeout > 0) {
            setTimeout(() => dismiss(id), timeout);
        }
    }

    return {
        toasts,
        dismiss,
        push,
        success: (message: string) => push(message, 'success'),
        error: (message: string) => push(message, 'error', 7000),
        info: (message: string) => push(message, 'info'),
    };
}
