<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeding Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@msc.com'],
            [
                'name' => 'Super Admin MSC',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'phone' => '08123456789',
                'status' => 'active',
            ]
        );

        // Seeding Admin (Pelatih)
        User::firstOrCreate(
            ['email' => 'coach@msc.com'],
            [
                'name' => 'Pelatih MSC',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '08123456788',
                'status' => 'active',
            ]
        );

        // Seeding User (Orang Tua)
        User::firstOrCreate(
            ['email' => 'parent@msc.com'],
            [
                'name' => 'Orang Tua Atlet',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '08123456787',
                'status' => 'active',
            ]
        );
    }
}
