<?php

namespace Database\Seeders;

use App\Enums\DatePreference;
use App\Models\Insurer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name' => 'HealthFirst Insurance',
                'code' => 'INS-A',
                'base_processing_cost' => 10.00,
                'daily_capacity' => 100,
                'min_batch_size' => 5,
                'max_batch_size' => 50,
                'date_preference' => DatePreference::ENCOUNTER,
                'specialty_efficiency_multipliers' => [
                    'cardiology' => 0.8,      // 20% more efficient
                    'orthopedics' => 1.0,
                    'neurology' => 1.2,       // 20% less efficient
                    'pediatrics' => 0.9,
                    'oncology' => 1.3,
                    'dermatology' => 0.85,
                    'radiology' => 1.0,
                    'psychiatry' => 1.1,
                    'gastroenterology' => 1.15,
                    'endocrinology' => 1.25,
                ],
            ],
            [
                'name' => 'MediCare Plus',
                'code' => 'INS-B',
                'base_processing_cost' => 15.00,
                'daily_capacity' => 80,
                'min_batch_size' => 3,
                'max_batch_size' => 40,
                'date_preference' => DatePreference::SUBMISSION,
                'specialty_efficiency_multipliers' => [
                    'cardiology' => 1.1,
                    'orthopedics' => 0.75,    // 25% more efficient
                    'neurology' => 1.0,
                    'pediatrics' => 0.8,
                    'oncology' => 1.4,
                    'dermatology' => 1.0,
                    'radiology' => 0.9,
                    'psychiatry' => 1.3,
                    'gastroenterology' => 1.0,
                    'endocrinology' => 1.2,
                ],
            ],
            [
                'name' => 'United Health Partners',
                'code' => 'INS-C',
                'base_processing_cost' => 12.50,
                'daily_capacity' => 120,
                'min_batch_size' => 10,
                'max_batch_size' => 60,
                'date_preference' => DatePreference::ENCOUNTER,
                'specialty_efficiency_multipliers' => [
                    'cardiology' => 1.0,
                    'orthopedics' => 1.1,
                    'neurology' => 0.85,      // 15% more efficient
                    'pediatrics' => 1.0,
                    'oncology' => 0.9,
                    'dermatology' => 1.2,
                    'radiology' => 0.8,       // 20% more efficient
                    'psychiatry' => 0.95,
                    'gastroenterology' => 1.1,
                    'endocrinology' => 1.0,
                ],
            ],
            [
                'name' => 'Premier Care Insurance',
                'code' => 'INS-D',
                'base_processing_cost' => 20.00,
                'daily_capacity' => 60,
                'min_batch_size' => 2,
                'max_batch_size' => 30,
                'date_preference' => DatePreference::SUBMISSION,
                'specialty_efficiency_multipliers' => [
                    'cardiology' => 1.2,
                    'orthopedics' => 1.3,
                    'neurology' => 1.1,
                    'pediatrics' => 0.7,      // 30% more efficient
                    'oncology' => 0.85,
                    'dermatology' => 0.9,
                    'radiology' => 1.0,
                    'psychiatry' => 0.8,      // 20% more efficient
                    'gastroenterology' => 1.05,
                    'endocrinology' => 0.95,
                ],
            ],
        ];

        foreach ($insurers as $insurerData) {
            Insurer::create($insurerData);
        }
    }
}
