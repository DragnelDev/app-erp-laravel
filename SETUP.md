# Setup local (backend-erp)

## Paso 1 — Entorno
```bash
cp .env.example .env
# Editar .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD (PostgreSQL local)
composer install
php artisan key:generate
php artisan migrate        # verifica la conexión a PostgreSQL
```

## Paso 2a — Autenticación (Breeze: Vue + Inertia + TypeScript + Pest + dark)
```bash
composer require laravel/breeze --dev
php artisan breeze:install vue --typescript --pest --dark
php artisan migrate
npm install
npm run build
php artisan test
```
Luego, en Breeze, eliminar la ruta pública de registro (los usuarios los crea un administrador).

## Paso 2b — RBAC (Spatie con teams = empresa)
Orden importante. Hacerlo después del paso 2a (Breeze).

1. Instalar y publicar:
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```
2. En `config/permission.php`:
```php
'teams' => true,
'column_names' => [
    // ...
    'team_foreign_key' => 'company_id',
],
```
3. En la migración publicada de Spatie, agregar la FK a `companies` en las 3 tablas que usan el equipo
   (`roles`, `model_has_permissions`, `model_has_roles`), junto a la columna `$columnNames['team_foreign_key']`:
```php
$table->foreign($columnNames['team_foreign_key'])->references('id')->on('companies')->cascadeOnDelete();
```
   (la columna en `roles` es nullable; la FK sigue siendo válida).
4. Registrar el middleware en `bootstrap/app.php`, dentro de `->withMiddleware(...)`
   (si Breeze ya agregó `$middleware->web(append: [...])`, añadir la clase a esa lista):
```php
$middleware->web(append: [\App\Http\Middleware\SetCurrentCompany::class]);
$middleware->prependToPriorityList(
    before: \Illuminate\Routing\Middleware\SubstituteBindings::class,
    prepend: \App\Http\Middleware\SetCurrentCompany::class,
);
```
5. Ruta para cambiar de empresa (dentro del grupo `auth` de `routes/web.php`):
```php
Route::post('/company/{company}/switch', \App\Http\Controllers\CompanySwitchController::class)->name('company.switch');
```
6. Migrar y sembrar (solo entorno local):
```bash
php artisan migrate
php artisan db:seed
```

### Usuarios de prueba (contraseña `password`)
| Email | Rol | Sucursales |
|---|---|---|
| admin.general@demo.test | Administrador General (2 empresas) | todas |
| admin.sucursal@demo.test | Administrador de Sucursal | Norte |
| ventas@demo.test | Ventas | Matriz, Norte |
| almacen@demo.test | Almacén | Matriz |
| contabilidad@demo.test | Contabilidad | Matriz, Norte |
| rrhh@demo.test | RRHH | Matriz, Norte |

### Cómo usar el aislamiento en modelos nuevos
- Por empresa (clientes, productos, categorías): `use BelongsToCompany;` y columna `company_id`.
- Por sucursal (almacenes, ventas, movimientos de stock): además `use BelongsToBranch;` y columna `branch_id`.
- Acceso a todas las sucursales = permiso `sucursales.acceso_total` (lo tiene el Administrador General).
