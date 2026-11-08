<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Support\Rbac\PermissionCatalog;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCompany
{
    public const SESSION_KEY = 'current_company_id';

    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $company = $this->resolveCompany($request, $user);

        if ($company === null) {
            // Sin empresa activa: el contexto queda sin resolver y los scopes no devuelven datos.
            return $next($request);
        }

        setPermissionsTeamId($company->id);
        $user->unsetRelation('roles')->unsetRelation('permissions');

        $allBranches = $user->checkPermissionTo(PermissionCatalog::ALL_BRANCHES_PERMISSION);

        $branchIds = $allBranches
            ? Branch::query()->where('company_id', $company->id)->where('is_active', true)->pluck('id')
            : $user->branches()->where('branches.company_id', $company->id)->where('branches.is_active', true)->pluck('branches.id');

        $this->context->set($company->id, $branchIds->map(fn ($id) => (int) $id)->all(), $allBranches);

        return $next($request);
    }

    private function resolveCompany(Request $request, User $user): ?Company
    {
        $companies = $user->companies()->where('companies.is_active', true);

        $selectedId = $request->session()->get(self::SESSION_KEY);

        $company = $selectedId !== null
            ? (clone $companies)->whereKey($selectedId)->first()
            : null;

        $company ??= $companies->orderBy('companies.id')->first();

        if ($company !== null) {
            $request->session()->put(self::SESSION_KEY, $company->id);
        }

        return $company;
    }
}
