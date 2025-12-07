<?php

namespace Database\Seeders;

use App\Models\Insurer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsurerSeeder extends Seeder
{
    private $insurers = [
        [
            'name' => 'Insurer A',
            'code' => 'INS-A',
            'email' => 'insurer-a@example.com',
            'daily_capacity' => 150,
            'min_batch_size' => 5,
            'max_batch_size' => 40,
            'date_preference' => 'encounter',
            'base_processing_cost' => 100.00,
            'specialty_efficiency' => [
                'cardiology' => 0.8,
                'orthopedics' => 1.2,
                'neurology' => 1.0,
                'dermatology' => 0.9,
                'general' => 1.0,
            ],
        ],
        [
            'name' => 'Insurer B',
            'code' => 'INS-B',
            'email' => 'insurer-b@example.com',
            'daily_capacity' => 120,
            'min_batch_size' => 8,
            'max_batch_size' => 50,
            'date_preference' => 'submission',
            'base_processing_cost' => 120.00,
            'specialty_efficiency' => [
                'cardiology' => 1.1,
                'orthopedics' => 0.85,
                'neurology' => 0.95,
                'dermatology' => 1.0,
                'general' => 1.1,
            ],
        ],
        [
            'name' => 'Insurer C',
            'code' => 'INS-C',
            'email' => 'insurer-c@example.com',
            'daily_capacity' => 100,
            'min_batch_size' => 10,
            'max_batch_size' => 35,
            'date_preference' => 'encounter',
            'base_processing_cost' => 90.00,
            'specialty_efficiency' => [
                'cardiology' => 0.9,
                'orthopedics' => 1.0,
                'neurology' => 0.85,
                'dermatology' => 0.95,
                'general' => 1.0,
            ],
        ],
        [
            'name' => 'Insurer D',
            'code' => 'INS-D',
            'email' => 'insurer-d@example.com',
            'daily_capacity' => 180,
            'min_batch_size' => 5,
            'max_batch_size' => 60,
            'date_preference' => 'submission',
            'base_processing_cost' => 110.00,
            'specialty_efficiency' => [
                'cardiology' => 1.0,
                'orthopedics' => 0.9,
                'neurology' => 1.1,
                'dermatology' => 1.0,
                'general' => 0.95,
            ],
        ],
    ];

    public function run(): void
    {
        foreach ($this->insurers as $insurer) {
            Insurer::create($insurer);
        }
    }
}
