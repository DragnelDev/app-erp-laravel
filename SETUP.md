# Setup local

## Puesta en marcha

```bash
cp .env.example .env          # completar DB_DATABASE, DB_USERNAME, DB_PASSWORD (PostgreSQL local)
composer install
php artisan key:generate
php artisan migrate:fresh --seed   # solo en local: crea empresas, roles y usuarios de prueba
npm install
npm run build                      # necesario antes de correr los tests (manifest de Vite)
php artisan test
composer run dev                   # o: php artisan serve + npm run dev
```

## Usuarios de prueba (contraseña `password`)

| Email                    | Rol                                | Sucursales    |
| ------------------------ | ---------------------------------- | ------------- |
| admin.general@demo.test  | Administrador General (2 empresas) | todas         |
| admin.sucursal@demo.test | Administrador de Sucursal          | Norte         |
| ventas@demo.test         | Ventas                             | Matriz, Norte |
| almacen@demo.test        | Almacén                            | Matriz        |
| contabilidad@demo.test   | Contabilidad                       | Matriz, Norte |
| rrhh@demo.test           | RRHH                               | Matriz, Norte |

## Multiempresa / sucursales

- Spatie `teams` = empresa (`company_id`). Roles por empresa; permisos globales con formato `modulo.accion`.
- Catálogo de permisos y roles: `app/Support/Rbac/PermissionCatalog.php`. Al crear una empresa, usar `SyncCompanyRoles`.
- Aislamiento en modelos nuevos: `BelongsToCompany` (clientes, productos, categorías) y además `BelongsToBranch`
  (almacenes, ventas, movimientos de stock). Acceso a todas las sucursales = permiso `sucursales.acceso_total`.

## UI

- Tailwind 3 (Breeze) con `darkMode: 'class'`. Paleta 60-30-10: slate (neutros/superficies) + blue (acento) + emerald/rose (estados).
- Tema: `composables/useTheme.ts` (claro/oscuro/sistema, `localStorage: theme`), script anti-parpadeo en `resources/views/app.blade.php`.
- Navegación: `resources/js/config/navigation.ts` alimenta el sidebar y el buscador Cmd/Ctrl+K (filtrado por permisos).
- Componentes: `Card`, `StatusBadge` (Borrador/Facturado/Anulado), `Skeleton`/`PageSkeleton`, `FormField`, `ToastContainer`
    - `useToast()`. Los mensajes `success`/`error`/`info` en la sesión flash se muestran como toasts.
