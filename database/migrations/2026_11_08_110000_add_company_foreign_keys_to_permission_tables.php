<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Integridad referencial del "team" de Spatie (company_id -> companies).
 * Va aparte de la migración publicada de Spatie para no modificarla.
 */
return new class extends Migration
{
    public function up(): void
    {
        $teamKey = config('permission.column_names.team_foreign_key');

        foreach (['roles', 'model_has_permissions', 'model_has_roles'] as $table) {
            Schema::table(config("permission.table_names.{$table}"), function (Blueprint $blueprint) use ($teamKey) {
                $blueprint->foreign($teamKey)->references('id')->on('companies')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $teamKey = config('permission.column_names.team_foreign_key');

        foreach (['roles', 'model_has_permissions', 'model_has_roles'] as $table) {
            Schema::table(config("permission.table_names.{$table}"), function (Blueprint $blueprint) use ($teamKey) {
                $blueprint->dropForeign([$teamKey]);
            });
        }
    }
};
