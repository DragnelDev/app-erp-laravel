export interface NavItem {
    label: string;
    routeName: string;
    icon: string;
    /** Permiso requerido (formato modulo.accion). Sin permiso = visible para todos. */
    permission?: string;
    keywords?: string[];
}

export interface NavGroup {
    label: string;
    items: NavItem[];
}

/**
 * Agregar aquí cada módulo nuevo cuando exista su ruta, por ejemplo:
 * { label: 'Productos', routeName: 'products.index', icon: 'cube', permission: 'productos.ver' }
 */
export const navigation: NavGroup[] = [
    {
        label: 'General',
        items: [
            {
                label: 'Dashboard',
                routeName: 'dashboard',
                icon: 'home',
                keywords: ['inicio', 'resumen'],
            },
        ],
    },
    {
        label: 'Cuenta',
        items: [
            {
                label: 'Perfil',
                routeName: 'profile.edit',
                icon: 'user',
                keywords: ['contraseña', 'cuenta'],
            },
        ],
    },
];
