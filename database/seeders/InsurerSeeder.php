<?php

namespace Database\Seeders;

use App\Enums\DatePreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $insurers = [
            [
                'name' => 'Insurer A',
                'code' => 'INS-A',
                'email' => 'claims@insurer-a.com',
                'min_batch_size' => 5,
                'max_batch_size' => 40,
                'daily_capacity_limit' => 150,
                'date_preference' => DatePreference::ENCOUNTER,
                'base_processing_cost' => 3500.00,
                'time_of_month_multiplier_start' => 0.15,
                'time_of_month_multiplier_end' => 0.45,
                'priority_cost_multipliers' => json_encode([
                    1 => 0.8,
                    2 => 1.0,
                    3 => 1.1,
                    4 => 1.3,
                    5 => 1.6,
                ]),
                'specialty_cost_multipliers' => json_encode([
                    'cardiology' => 1.3,
                    'orthopedics' => 1.1,
                ]),
                'claim_value_multipliers' => json_encode([
                    [
                        'min' => 0,
                        'max' => 1000,
                        'multiplier' => 0.9,
                    ],
                    [
                        'min' => 1000,
                        'max' => 5000,
                        'multiplier' => 1.0,
                    ],
                    [
                        'min' => 5000,
                        'max' => 10000,
                        'multiplier' => 1.2,
                    ],
                    [
                        'min' => 10000,
                        'max' => null,
                        'multiplier' => 1.5,
                    ],
                ]),
            ],
            [
                'name' => 'Insurer B',
                'code' => 'INS-B',
                'email' => 'processing@insurer-b.com',
                'min_batch_size' => 10,
                'max_batch_size' => 60,
                'daily_capacity_limit' => 200,
                'date_preference' => DatePreference::SUBMISSION,
                'base_processing_cost' => 2800.00,
                'time_of_month_multiplier_start' => 0.25,
                'time_of_month_multiplier_end' => 0.55,
                'priority_cost_multipliers' => json_encode([
                    1 => 0.7,
                    2 => 1.0,
                    3 => 1.1,
                    4 => 1.4,
                    5 => 1.8,
                ]),
                'specialty_cost_multipliers' => json_encode([
                    'cardiology' => 1.1,
                    'orthopedics' => 1.5,
                ]),
                'claim_value_multipliers' => json_encode([
                    [
                        'min' => 0,
                        'max' => 500,
                        'multiplier' => 0.8,
                    ],
                    [
                        'min' => 500,
                        'max' => 2000,
                        'multiplier' => 0.9,
                    ],
                    [
                        'min' => 2000,
                        'max' => 8000,
                        'multiplier' => 1.1,
                    ],
                    [
                        'min' => 8000,
                        'max' => null,
                        'multiplier' => 1.4,
                    ],
                ]),
            ],
            [
                'name' => 'Insurer C',
                'code' => 'INS-C',
                'email' => 'batches@insurer-c.com',
                'min_batch_size' => 8,
                'max_batch_size' => 50,
                'daily_capacity_limit' => 180,
                'date_preference' => DatePreference::ENCOUNTER,
                'base_processing_cost' => 3700.00,
                'time_of_month_multiplier_start' => 0.20,
                'time_of_month_multiplier_end' => 0.50,
                'priority_cost_multipliers' => json_encode([
                    1 => 0.75,
                    2 => 0.95,
                    3 => 1.05,
                    4 => 1.35,
                    5 => 1.7,
                ]),
                'specialty_cost_multipliers' => json_encode([
                    'cardiology' => 1.4,
                    'orthopedics' => 1.2,
                ]),
                'claim_value_multipliers' => json_encode([
                    [
                        'min' => 0,
                        'max' => 2000,
                        'multiplier' => 0.85,
                    ],
                    [
                        'min' => 2000,
                        'max' => 7500,
                        'multiplier' => 1.05,
                    ],
                    [
                        'min' => 7500,
                        'max' => null,
                        'multiplier' => 1.6,
                    ],
                ]),
            ],
            [
                'name' => 'Insurer D',
                'code' => 'INS-D',
                'email' => 'submissions@insurer-d.com',
                'min_batch_size' => 12,
                'max_batch_size' => 75,
                'daily_capacity_limit' => 250,
                'date_preference' => DatePreference::SUBMISSION,
                'base_processing_cost' => 2600.00,
                'time_of_month_multiplier_start' => 0.30,
                'time_of_month_multiplier_end' => 0.60,
                'priority_cost_multipliers' => json_encode([
                    1 => 0.6,
                    2 => 1.0,
                    3 => 1.2,
                    4 => 1.5,
                    5 => 1.9,
                ]),
                'specialty_cost_multipliers' => json_encode([
                    'cardiology' => 1.0,
                    'orthopedics' => 1.6,
                ]),
                'claim_value_multipliers' => json_encode([
                    [
                        'min' => 0,
                        'max' => 1500,
                        'multiplier' => 0.75,
                    ],
                    [
                        'min' => 1500,
                        'max' => 6000,
                        'multiplier' => 0.95,
                    ],
                    [
                        'min' => 6000,
                        'max' => 15000,
                        'multiplier' => 1.3,
                    ],
                    [
                        'min' => 15000,
                        'max' => null,
                        'multiplier' => 1.8,
                    ],
                ]),
            ],
        ];

        DB::table('insurers')->insert(
            collect($insurers)->map(fn ($insurer) => [
                ...$insurer,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );
    }
}
