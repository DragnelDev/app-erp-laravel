<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Company;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(private readonly TenantContext $context) {}

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Props compartidas. Se usan closures para evaluarlas al renderizar,
     * cuando SetCurrentCompany ya resolvió la empresa activa.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'permissions' => fn () => $request->user()
                    ? $request->user()->getAllPermissions()->pluck('name')->values()->all()
                    : [],
            ],
            'tenancy' => fn () => $this->tenancy($request),
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'info' => $request->session()->get('info'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function tenancy(Request $request): array
    {
        $user = $request->user();

        if ($user === null || ! $this->context->isResolved()) {
            return ['company' => null, 'companies' => [], 'branches' => [], 'all_branches' => false];
        }

        return [
            'company' => Company::query()
                ->select(['id', 'name', 'base_currency', 'foreign_currency', 'exchange_rate'])
                ->find($this->context->companyId()),
            'companies' => $user->companies()
                ->where('companies.is_active', true)
                ->orderBy('companies.name')
                ->get(['companies.id', 'companies.name'])
                ->map(fn (Company $company) => ['id' => $company->id, 'name' => $company->name])
                ->values(),
            'branches' => Branch::query()
                ->whereIn('id', $this->context->branchIds())
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'all_branches' => $this->context->hasAllBranches(),
        ];
    }
}
