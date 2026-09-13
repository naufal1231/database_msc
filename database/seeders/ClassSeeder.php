<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SwimClass;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                'name' => 'Dasar 0',
                'description' => 'Tahap pengenalan air',
            ],
            [
                'name' => 'Dasar 1',
                'description' => 'Tahap dasar teknik renang',
            ],
            [
                'name' => 'Pemula',
                'description' => 'Penguasaan 4 gaya dasar',
            ],
            [
                'name' => 'Pra-Prestasi',
                'description' => 'Persiapan kompetisi',
            ],
            [
                'name' => 'Prestasi',
                'description' => 'Atlet kompetitif aktif',
            ],
        ];

        foreach ($classes as $class) {
            SwimClass::firstOrCreate(
                ['name' => $class['name']],
                ['description' => $class['description']]
            );
        }
    }
}
