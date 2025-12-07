<?php

namespace Database\Seeders;

use App\Models\Insurer;
use Illuminate\Database\Seeder;

class InsurerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insurers = [
            [
                'name' => 'Blue Cross',
                'code' => 'BC001',
                'min_batch_size' => 10,
                'max_batch_size' => 100,
                'daily_processing_capacity' => 200,
                'date_preference' => 'encounter',
                'specialty_factors' => json_encode(['cardiology' => 1.2, 'orthopedics' => 1.0]),
                'email' => 'claims@bluecross.example',
            ],
            [
                'name' => 'Aetna',
                'code' => 'AE002',
                'min_batch_size' => 50,
                'max_batch_size' => 500,
                'daily_processing_capacity' => 1000,
                'date_preference' => 'submission',
                'specialty_factors' => json_encode(['cardiology' => 1.0, 'general' => 0.8]),
                'email' => 'claims@aetna.example',
            ],
            [
                'name' => 'SafeGuard',
                'code' => 'SG003',
                'min_batch_size' => 20,
                'max_batch_size' => 150,
                'daily_processing_capacity' => 300,
                'date_preference' => 'encounter',
                'specialty_factors' => json_encode(['dermatology' => 1.5]),
                'email' => 'claims@safeguard.example',
            ]
        ];

        foreach ($insurers as $insurer) {
            Insurer::updateOrCreate(['code' => $insurer['code']], $insurer);
        }
    }
}
