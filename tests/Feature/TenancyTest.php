<?php

use App\Actions\Companies\SyncCompanyRoles;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Support\Rbac\PermissionCatalog as P;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * @return array{0: User, 1: Company, 2: Branch, 3: Branch}
 */
function makeTenantUser(string $role, bool $assignFirstBranch = true): array
{
    $company = Company::create(['name' => 'Acme', 'exchange_rate' => '6.96']);
    $branchA = Branch::create(['company_id' => $company->id, 'name' => 'Sucursal A', 'code' => 'A']);
    $branchB = Branch::create(['company_id' => $company->id, 'name' => 'Sucursal B', 'code' => 'B']);

    app(SyncCompanyRoles::class)->handle($company);

    $user = User::factory()->create();
    $user->companies()->attach($company->id);

    if ($assignFirstBranch) {
        $user->branches()->attach($branchA->id);
    }

    setPermissionsTeamId($company->id);
    $user->assignRole($role);
    setPermissionsTeamId(null);

    return [$user, $company, $branchA, $branchB];
}

test('el administrador de sucursal solo ve sus sucursales asignadas', function () {
    [$user, $company] = makeTenantUser(P::ROLE_ADMIN_SUCURSAL);

    $this->actingAs($user)->get('/dashboard')->assertOk()->assertInertia(
        fn (Assert $page) => $page
            ->where('tenancy.company.id', $company->id)
            ->where('tenancy.all_branches', false)
            ->has('tenancy.branches', 1)
    );
});

test('el administrador general ve todas las sucursales de la empresa', function () {
    [$user, $company] = makeTenantUser(P::ROLE_ADMIN_GENERAL, assignFirstBranch: false);

    $this->actingAs($user)->get('/dashboard')->assertOk()->assertInertia(
        fn (Assert $page) => $page
            ->where('tenancy.company.id', $company->id)
            ->where('tenancy.all_branches', true)
            ->has('tenancy.branches', 2)
    );
});

test('no se puede cambiar a una empresa a la que no se pertenece', function () {
    [$user] = makeTenantUser(P::ROLE_VENTAS);
    $other = Company::create(['name' => 'Otra', 'exchange_rate' => '6.96']);

    $this->actingAs($user)->post(route('company.switch', $other))->assertForbidden();
});

test('un usuario sin empresa activa no recibe datos de empresa', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertOk()->assertInertia(
        fn (Assert $page) => $page->where('tenancy.company', null)
    );
});
