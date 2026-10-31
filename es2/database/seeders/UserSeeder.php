<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one user for each role. The password is "password" for everyone.
        $password = Hash::make('password');

        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Anna Conti', 'email' => 'admin@example.com', 'role' => 'admin', 'password' => $password, 'created_at' => '2026-01-02 09:00:00', 'updated_at' => '2026-01-02 09:00:00'],
            ['id' => 2, 'name' => 'Marco Rinaldi', 'email' => 'manager@example.com', 'role' => 'manager', 'password' => $password, 'created_at' => '2026-01-02 09:00:00', 'updated_at' => '2026-01-02 09:00:00'],
            ['id' => 3, 'name' => 'Giulia Ferri', 'email' => 'marketing@example.com', 'role' => 'marketing', 'password' => $password, 'created_at' => '2026-01-02 09:00:00', 'updated_at' => '2026-01-02 09:00:00'],
            ['id' => 4, 'name' => 'System', 'email' => 'system@example.com', 'role' => 'system', 'password' => $password, 'created_at' => '2026-01-02 09:00:00', 'updated_at' => '2026-01-02 09:00:00'],
        ]);
    }
}