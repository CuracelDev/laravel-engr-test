<?php

namespace Database\Seeders;

use App\Models\Insurer;
use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtyEfficiencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $specialties = Specialty::all();
        $insurers = Insurer::all();

        foreach ($insurers as $insurer) {
            foreach ($specialties as $specialty) {
                $rand = rand(0,2);
                $data[] = [
                    'specialty_id' => $specialty->id,
                    'insurers_id' => $insurer->id,
                    'efficiency' => "$rand", // Generate random values between 0, 1, 2
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $chunks = array_chunk($data, 20);
        foreach ($chunks as $chunk) {
            DB::table('insurer_specialty_efficiencies')->insert($chunk);
        }
    }
}
