<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Los roles se crean por empresa (SyncCompanyRoles); aquí solo datos de desarrollo.
        $this->call(DevelopmentSeeder::class);
    }
}
