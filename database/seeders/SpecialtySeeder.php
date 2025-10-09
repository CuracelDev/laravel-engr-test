<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specialty::create(['name' => 'Cardiology', 'processing_cost_multiplier' => 1.0]);
        Specialty::create(['name' => 'Orthopedics', 'processing_cost_multiplier' => 1.2]);
        Specialty::create(['name' => 'Neurology', 'processing_cost_multiplier' => 1.1]);
        Specialty::create(['name' => 'Pediatrics', 'processing_cost_multiplier' => 1.05]);
        Specialty::create(['name' => 'Dermatology', 'processing_cost_multiplier' => 1.15]);
        Specialty::create(['name' => 'Gastroenterology', 'processing_cost_multiplier' => 1.2]);
        Specialty::create(['name' => 'Psychiatry', 'processing_cost_multiplier' => 1.3]);
        Specialty::create(['name' => 'Rheumatology', 'processing_cost_multiplier' => 1.25]);
        Specialty::create(['name' => 'Nephrology', 'processing_cost_multiplier' => 1.18]);
        Specialty::create(['name' => 'Endocrinology', 'processing_cost_multiplier' => 1.1]);
    }
}
