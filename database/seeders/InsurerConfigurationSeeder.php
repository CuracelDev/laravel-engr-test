<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'insurer_code' => 'INS-A',
                'daily_capacity' => 150,
                'min_batch_size' => 5,
                'max_batch_size' => 25,
                'date_preference' => 'encounter',
                'specialty_efficiency' => json_encode([
                    'cardiology' => 0.8,
                    'orthopedics' => 1.2,
                    'neurology' => 0.9,
                    'general' => 1.0,
                    'pediatrics' => 0.85,
                ]),
                'priority_multiplier' => 1.00,
                'value_multiplier' => 0.0001,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'insurer_code' => 'INS-B',
                'daily_capacity' => 200,
                'min_batch_size' => 3,
                'max_batch_size' => 40,
                'date_preference' => 'submission',
                'specialty_efficiency' => json_encode([
                    'cardiology' => 1.1,
                    'orthopedics' => 0.9,
                    'neurology' => 1.3,
                    'general' => 1.0,
                    'pediatrics' => 0.7,
                ]),
                'priority_multiplier' => 1.10,
                'value_multiplier' => 0.00008,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'insurer_code' => 'INS-C',
                'daily_capacity' => 100,
                'min_batch_size' => 2,
                'max_batch_size' => 30,
                'date_preference' => 'encounter',
                'specialty_efficiency' => json_encode([
                    'cardiology' => 0.95,
                    'orthopedics' => 0.85,
                    'neurology' => 1.1,
                    'general' => 1.05,
                    'pediatrics' => 1.2,
                ]),
                'priority_multiplier' => 0.95,
                'value_multiplier' => 0.00012,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'insurer_code' => 'INS-D',
                'daily_capacity' => 250,
                'min_batch_size' => 1,
                'max_batch_size' => 50,
                'date_preference' => 'submission',
                'specialty_efficiency' => json_encode([
                    'cardiology' => 1.0,
                    'orthopedics' => 1.0,
                    'neurology' => 1.0,
                    'general' => 0.9,
                    'pediatrics' => 1.1,
                ]),
                'priority_multiplier' => 1.05,
                'value_multiplier' => 0.0001,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('insurer_configurations')->insert($configurations);
    }
}
