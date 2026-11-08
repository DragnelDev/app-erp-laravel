<?php

namespace App\Models\Concerns;

use App\Models\Branch;
use App\Support\Tenancy\BranchScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Usar junto con BelongsToCompany en modelos filtrados por sucursal
 * (almacenes, pedidos de venta, movimientos de stock).
 */
trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
