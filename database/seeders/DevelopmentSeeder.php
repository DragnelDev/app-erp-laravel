<?php

namespace Database\Seeders;

use App\Actions\Companies\SyncCompanyRoles;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Support\Rbac\PermissionCatalog as P;
use Illuminate\Database\Seeder;

/**
 * Datos SOLO para desarrollo local. Contraseña de todos los usuarios: "password".
 */
class DevelopmentSeeder extends Seeder
{
    public function run(SyncCompanyRoles $syncRoles): void
    {
        if (! app()->environment('local')) {
            $this->command?->warn('DevelopmentSeeder omitido: solo corre en entorno local.');

            return;
        }

        $demo = Company::updateOrCreate(['tax_id' => '1000000010'], [
            'name' => 'Empresa Demo S.R.L.',
            'exchange_rate' => '6.960000',
        ]);
        $matriz = Branch::updateOrCreate(['company_id' => $demo->id, 'code' => 'MTZ'], ['name' => 'Casa Matriz']);
        $norte = Branch::updateOrCreate(['company_id' => $demo->id, 'code' => 'NTE'], ['name' => 'Sucursal Norte']);

        $second = Company::updateOrCreate(['tax_id' => '1000000020'], [
            'name' => 'Empresa Demo 2 S.A.',
            'exchange_rate' => '6.960000',
        ]);
        $second2 = Branch::updateOrCreate(['company_id' => $second->id, 'code' => 'MTZ'], ['name' => 'Casa Matriz']);

        $syncRoles->handle($demo);
        $syncRoles->handle($second);

        // usuario => [rol, sucursales de la empresa demo]
        $users = [
            'admin.general@demo.test' => ['Admin General', P::ROLE_ADMIN_GENERAL, []],
            'admin.sucursal@demo.test' => ['Admin Sucursal Norte', P::ROLE_ADMIN_SUCURSAL, [$norte]],
            'ventas@demo.test' => ['Usuario Ventas', P::ROLE_VENTAS, [$matriz, $norte]],
            'almacen@demo.test' => ['Usuario Almacén', P::ROLE_ALMACEN, [$matriz]],
            'contabilidad@demo.test' => ['Usuario Contabilidad', P::ROLE_CONTABILIDAD, [$matriz, $norte]],
            'rrhh@demo.test' => ['Usuario RRHH', P::ROLE_RRHH, [$matriz, $norte]],
        ];

        foreach ($users as $email => [$name, $role, $branches]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'email_verified_at' => now()]
            );

            $user->companies()->syncWithoutDetaching([
                $demo->id => ['default_branch_id' => ($branches[0] ?? $matriz)->id],
            ]);
            $user->branches()->sync(collect($branches)->pluck('id')->all());

            $this->assignRole($user, $demo, $role);
        }

        // El administrador general también administra la segunda empresa (prueba multiempresa).
        $general = User::where('email', 'admin.general@demo.test')->firstOrFail();
        $general->companies()->syncWithoutDetaching([
            $second->id => ['default_branch_id' => $second2->id],
        ]);
        $this->assignRole($general, $second, P::ROLE_ADMIN_GENERAL);
    }

    private function assignRole(User $user, Company $company, string $role): void
    {
        setPermissionsTeamId($company->id);
        $user->unsetRelation('roles')->unsetRelation('permissions');
        $user->syncRoles([$role]);
    }
}
