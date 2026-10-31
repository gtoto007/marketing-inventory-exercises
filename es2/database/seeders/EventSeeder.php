<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 events. The names are made up, the places are real.
        // Today is 2026-10-30: event 1 is finished, event 2 is still going on.
        DB::table('events')->insert([
            ['id' => 1, 'name' => 'Fiera Innovazione Retail 2026', 'description' => 'Fiera dedicata alla tecnologia per il retail e al marketing in negozio.', 'start_date' => '2026-09-22', 'end_date' => '2026-09-24', 'venue_name' => 'Fiera Milano Rho', 'address_line' => 'Strada Statale del Sempione 28', 'city' => 'Rho', 'postal_code' => '20017', 'country_code' => 'IT', 'created_at' => '2026-07-01 10:00:00', 'updated_at' => '2026-07-01 10:00:00'],
            ['id' => 2, 'name' => 'Forum Esperienza di Marca 2026', 'description' => 'Forum di quattro giorni sull\'attivazione del marchio e sull\'esperienza del cliente.', 'start_date' => '2026-10-28', 'end_date' => '2026-10-31', 'venue_name' => 'MiCo Milano Convention Centre', 'address_line' => 'Viale Eginardo', 'city' => 'Milano', 'postal_code' => '20149', 'country_code' => 'IT', 'created_at' => '2026-08-03 10:00:00', 'updated_at' => '2026-08-03 10:00:00'],
        ]);
    }
}