<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Fill the database with example data.
     *
     * The order is important: each seeder uses the ids created by the seeders before it.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CatalogSeeder::class,
            CarrierSeeder::class,
            EventSeeder::class,
            ProcurementSeeder::class,
            InventorySeeder::class,
        ]);
    }
}