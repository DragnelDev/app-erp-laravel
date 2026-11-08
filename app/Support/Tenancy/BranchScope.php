<?php

namespace App\Support\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtra por las sucursales a las que el usuario tiene acceso en la empresa activa.
 */
class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $context = app(TenantContext::class);

        if ($context->isResolved()) {
            $builder->whereIn($model->qualifyColumn('branch_id'), $context->branchIds());

            return;
        }

        if (! app()->runningInConsole()) {
            $builder->whereRaw('1 = 0');
        }
    }
}
