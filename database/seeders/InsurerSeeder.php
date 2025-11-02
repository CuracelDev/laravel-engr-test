<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Insurer;

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
                'email' => 'june.munoz94@eldoriaan.com',
                'min_batch_size' => 3,
                'max_batch_size' => 10,
                'daily_capacity' => 5,
                'batch_by' => 'encounter_date',
                'specialty_efficiency' => [
                    'Cardiology' => 1.2,
                    'Orthopedics' => 1.1,
                    'General' => 1.0,
                ],
                'priority_multipliers' => [
                    'low' => 0.8,
                    'medium' => 1.0,
                    'high' => 1.5,
                ],
                'value_tiers' => [
                    ['min' => 0, 'max' => 1000, 'cost_multiplier' => 1.0],
                    ['min' => 1001, 'max' => 5000, 'cost_multiplier' => 1.2],
                    ['min' => 5001, 'max' => PHP_INT_MAX, 'cost_multiplier' => 1.5],
                ],
                'base_cost' => 1.00,
                'monthly_cost_increase' => 0.20,
            ],
            [
                'name' => 'Insurer B',
                'code' => 'INS-B',
                'email' => 'alease.campos1987@eldoriaan.com',
                'min_batch_size' => 2,
                'max_batch_size' => 8,
                'daily_capacity' => 4,
                'batch_by' => 'submission_date',
                'specialty_efficiency' => [
                    'Cardiology' => 1.3,
                    'Orthopedics' => 1.0,
                ],
                'priority_multipliers' => [
                    'low' => 0.9,
                    'medium' => 1.0,
                    'high' => 1.4,
                ],
                'value_tiers' => [
                    ['min' => 0, 'max' => 2000, 'cost_multiplier' => 1.0],
                    ['min' => 2001, 'max' => PHP_INT_MAX, 'cost_multiplier' => 1.3],
                ],
                'base_cost' => 1.10,
                'monthly_cost_increase' => 0.25,
            ],
            [
                'name' => 'Insurer C',
                'code' => 'INS-C',
                'email' => 'rylee.collins1975@thecyclesolutions.com',
                'min_batch_size' => 4,
                'max_batch_size' => 15,
                'daily_capacity' => 6,
                'batch_by' => 'encounter_date',
                'base_cost' => 0.90,
                'monthly_cost_increase' => 0.15,
            ],
            [
                'name' => 'Insurer D',
                'code' => 'INS-D',
                'email' => 'kai.chang80@voiceglobals.com',
                'min_batch_size' => 2,
                'max_batch_size' => 12,
                'daily_capacity' => 5,
                'batch_by' => 'submission_date',
                'base_cost' => 1.05,
                'monthly_cost_increase' => 0.22,
            ],
        ];

        foreach ($insurers as $insurer) {
            Insurer::updateOrCreate(
                ['code' => $insurer['code']],
                $insurer
            );
        }
    }
}