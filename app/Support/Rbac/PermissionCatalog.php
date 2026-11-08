<?php

namespace App\Support\Rbac;

/**
 * Catálogo de permisos (formato modulo.accion) y roles por defecto de cada empresa.
 * Ajustar aquí; luego ejecutar de nuevo el seeder / SyncCompanyRoles.
 */
final class PermissionCatalog
{
    public const ALL_BRANCHES_PERMISSION = 'sucursales.acceso_total';

    public const ROLE_ADMIN_GENERAL = 'Administrador General';

    public const ROLE_ADMIN_SUCURSAL = 'Administrador de Sucursal';

    public const ROLE_VENTAS = 'Ventas';

    public const ROLE_ALMACEN = 'Almacén';

    public const ROLE_CONTABILIDAD = 'Contabilidad';

    public const ROLE_RRHH = 'RRHH';

    private const CRUD = ['ver', 'crear', 'editar', 'eliminar'];

    /** @var array<string, list<string>> */
    private const MODULES = [
        'empresas' => ['ver', 'crear', 'editar'],
        'sucursales' => ['ver', 'crear', 'editar', 'acceso_total'],
        'usuarios' => ['ver', 'crear', 'editar', 'eliminar', 'asignar_roles'],
        'roles' => ['ver', 'gestionar'],
        'clientes' => self::CRUD,
        'leads' => self::CRUD,
        'interacciones' => self::CRUD,
        'categorias' => self::CRUD,
        'productos' => self::CRUD,
        'almacenes' => self::CRUD,
        'inventario' => ['ver', 'ajustar'],
        'ventas' => ['ver', 'crear', 'editar', 'anular', 'facturar'],
        'kardex' => ['ver'],
        'rrhh' => ['ver', 'gestionar'],
    ];

    /** @return list<string> */
    public static function permissions(): array
    {
        $all = [];
        foreach (self::MODULES as $module => $actions) {
            foreach ($actions as $action) {
                $all[] = "{$module}.{$action}";
            }
        }

        return $all;
    }

    /**
     * @return array<string, list<string>> rol => permisos
     */
    public static function roles(): array
    {
        $all = self::permissions();

        $branchAdmin = array_values(array_diff($all, [
            'empresas.ver', 'empresas.crear', 'empresas.editar',
            'sucursales.crear', 'sucursales.acceso_total',
            'roles.gestionar',
        ]));

        return [
            self::ROLE_ADMIN_GENERAL => $all,
            self::ROLE_ADMIN_SUCURSAL => $branchAdmin,
            self::ROLE_VENTAS => self::expand([
                'clientes.*', 'leads.*', 'interacciones.*',
                'productos.ver', 'categorias.ver', 'ventas.ver', 'ventas.crear', 'ventas.editar',
            ]),
            self::ROLE_ALMACEN => self::expand([
                'productos.ver', 'categorias.ver', 'almacenes.ver',
                'inventario.*', 'kardex.ver',
            ]),
            self::ROLE_CONTABILIDAD => self::expand([
                'clientes.ver', 'productos.ver', 'kardex.ver',
                'ventas.ver', 'ventas.anular', 'ventas.facturar',
            ]),
            self::ROLE_RRHH => self::expand([
                'rrhh.*', 'usuarios.ver', 'usuarios.crear', 'usuarios.editar',
            ]),
        ];
    }

    /**
     * @param  list<string>  $patterns
     * @return list<string>
     */
    private static function expand(array $patterns): array
    {
        $all = self::permissions();
        $result = [];

        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '.*')) {
                $prefix = substr($pattern, 0, -1);
                foreach ($all as $permission) {
                    if (str_starts_with($permission, $prefix)) {
                        $result[] = $permission;
                    }
                }
            } else {
                $result[] = $pattern;
            }
        }

        return array_values(array_unique($result));
    }
}
