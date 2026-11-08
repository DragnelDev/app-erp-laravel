<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tax_id', 30)->nullable()->unique(); // NIT
            $table->char('base_currency', 3)->default('BOB');
            $table->char('foreign_currency', 3)->default('USD');
            // Bolivianos por 1 unidad de moneda extranjera (tipo de cambio fijo configurable)
            $table->decimal('exchange_rate', 12, 6);
            $table->jsonb('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
