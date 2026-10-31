<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarrierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 carriers. The names are real, the contacts are fake.
        DB::table('carriers')->insert([
            ['id' => 1, 'name' => 'BRT', 'email' => 'operations@brt.example.com', 'phone' => '+39 051 591 1100'],
            ['id' => 2, 'name' => 'GLS', 'email' => 'operations@gls.example.com', 'phone' => '+39 02 8930 4400'],
            ['id' => 3, 'name' => 'DHL Express', 'email' => 'operations@dhl.example.com', 'phone' => '+39 02 4528 6200'],
            ['id' => 4, 'name' => 'SDA', 'email' => 'operations@sda.example.com', 'phone' => '+39 06 6640 3300'],
            ['id' => 5, 'name' => 'UPS', 'email' => 'operations@ups.example.com', 'phone' => '+39 02 3030 3900'],
        ]);
    }
}