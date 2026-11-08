<?php

namespace App\Actions\Companies;

use App\Models\Company;
use App\Support\Rbac\PermissionCatalog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea (o actualiza) los roles por defecto de una empresa con sus permisos.
 * Los permisos son globales; los roles pertenecen a la empresa (Spatie teams).
 */
class SyncCompanyRoles
{
    public function handle(Company $company): void
    {
        foreach (PermissionCatalog::permissions() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $previousTeam = getPermissionsTeamId();
        setPermissionsTeamId($company->id);

        foreach (PermissionCatalog::roles() as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissions);
        }

        setPermissionsTeamId($previousTeam);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
