<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Insurer Seeder
 * 
 * Seeds the database with 4 sample insurers, each with different:
 * - Processing cost multipliers (specialty, priority, value)
 * - Batch size constraints (min/max)
 * - Daily capacity limits
 * - Date preferences (encounter vs submission)
 */
class InsurerSeeder extends Seeder
{
    private $insurers = [
        [
            'name' => 'Insurer A',
            'code' => 'INS-A',
            'email' => 'processor@insurer-a.com',
            'specialty_costs' => [
                'cardiology' => 1.2,
                'orthopedics' => 1.0,
                'neurology' => 1.3,
                'general' => 0.9
            ],
            'priority_costs' => [1 => 1.0, 2 => 1.1, 3 => 1.3, 4 => 1.5, 5 => 2.0],
            'value_cost_multiplier' => 0.0001,
            'daily_capacity' => 150,
            'min_batch_size' => 5,
            'max_batch_size' => 30,
            'date_preference' => 'submission'
        ],
        [
            'name' => 'Insurer B',
            'code' => 'INS-B',
            'email' => 'claims@insurer-b.com',
            'specialty_costs' => [
                'cardiology' => 1.0,
                'orthopedics' => 1.1,
                'neurology' => 1.4,
                'general' => 0.8
            ],
            'priority_costs' => [1 => 1.0, 2 => 1.2, 3 => 1.4, 4 => 1.7, 5 => 2.2],
            'value_cost_multiplier' => 0.00015,
            'daily_capacity' => 100,
            'min_batch_size' => 3,
            'max_batch_size' => 25,
            'date_preference' => 'encounter'
        ],
        [
            'name' => 'Insurer C',
            'code' => 'INS-C',
            'email' => 'processing@insurer-c.com',
            'specialty_costs' => [
                'cardiology' => 1.1,
                'orthopedics' => 0.9,
                'neurology' => 1.2,
                'general' => 1.0
            ],
            'priority_costs' => [1 => 1.0, 2 => 1.15, 3 => 1.35, 4 => 1.6, 5 => 1.9],
            'value_cost_multiplier' => 0.00012,
            'daily_capacity' => 200,
            'min_batch_size' => 2,
            'max_batch_size' => 50,
            'date_preference' => 'submission'
        ],
        [
            'name' => 'Insurer D',
            'code' => 'INS-D',
            'email' => 'batches@insurer-d.com',
            'specialty_costs' => [
                'cardiology' => 0.95,
                'orthopedics' => 1.05,
                'neurology' => 1.1,
                'general' => 0.85
            ],
            'priority_costs' => [1 => 1.0, 2 => 1.08, 3 => 1.25, 4 => 1.45, 5 => 1.8],
            'value_cost_multiplier' => 0.0002,
            'daily_capacity' => 80,
            'min_batch_size' => 1,
            'max_batch_size' => 20,
            'date_preference' => 'encounter'
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->insurers as $insurer) {
            DB::table('insurers')->insert([
                'name' => $insurer['name'],
                'code' => $insurer['code'],
                'email' => $insurer['email'],
                'specialty_costs' => json_encode($insurer['specialty_costs']),
                'priority_costs' => json_encode($insurer['priority_costs']),
                'value_cost_multiplier' => $insurer['value_cost_multiplier'],
                'daily_capacity' => $insurer['daily_capacity'],
                'min_batch_size' => $insurer['min_batch_size'],
                'max_batch_size' => $insurer['max_batch_size'],
                'date_preference' => $insurer['date_preference'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
