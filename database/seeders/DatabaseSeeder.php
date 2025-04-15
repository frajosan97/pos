<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call other seeders if needed
        $this->call([
            CountyConstituencySeeder::class,
            CompanySeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            PaymentMethodSeeder::class,
        ]);
    }
}
