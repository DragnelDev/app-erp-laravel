<?php

namespace App\Support\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtra por company_id. En peticiones web sin empresa resuelta no devuelve nada
 * (falla cerrado); en consola (seeders, comandos) no filtra.
 */
class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $context = app(TenantContext::class);

        if ($context->isResolved()) {
            $builder->where($model->qualifyColumn('company_id'), $context->companyId());

            return;
        }

        if (! app()->runningInConsole()) {
            $builder->whereRaw('1 = 0');
        }
    }
}
