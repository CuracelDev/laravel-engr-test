<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            'Cardiology',
            'Neurology',
            'Orthopedics',
            'Pediatrics',
            'Oncology',
            'Dermatology',
            'Psychiatry',
            'Radiology',
            'Emergency Medicine',
            'General Surgery',
        ];

        foreach ($specialities as $speciality) {
            DB::table('specialties')->insert([
                'speciality' => $speciality,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
