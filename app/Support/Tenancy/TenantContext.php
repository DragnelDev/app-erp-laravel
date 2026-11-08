<?php

namespace App\Support\Tenancy;

/**
 * Empresa activa y sucursales permitidas para la petición actual.
 * Se registra como singleton "scoped" (se reinicia por petición).
 */
final class TenantContext
{
    private ?int $companyId = null;

    /** @var list<int> */
    private array $branchIds = [];

    private bool $allBranches = false;

    /**
     * @param  list<int>  $branchIds
     */
    public function set(int $companyId, array $branchIds, bool $allBranches): void
    {
        $this->companyId = $companyId;
        $this->branchIds = array_values(array_unique($branchIds));
        $this->allBranches = $allBranches;
    }

    public function isResolved(): bool
    {
        return $this->companyId !== null;
    }

    public function companyId(): ?int
    {
        return $this->companyId;
    }

    /** @return list<int> */
    public function branchIds(): array
    {
        return $this->branchIds;
    }

    public function hasAllBranches(): bool
    {
        return $this->allBranches;
    }

    public function canAccessBranch(int $branchId): bool
    {
        return in_array($branchId, $this->branchIds, true);
    }
}
