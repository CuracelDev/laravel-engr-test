<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('insurers')->delete();

        $insurers = [
            [
                'name' => 'Insurer A',
                'code' => 'INS-A',
                'notify_email' => 'ops+alpha@example.com',
                'daily_capacity' => 300,
                'min_batch_size' => 5,
                'max_batch_size' => 80,
                'date_preference' => 'submission',
                'time_cost_min' => 0.20,
                'time_cost_max' => 0.50,
                'specialty_multipliers' => json_encode([
                    'cardiology' => 0.9, 'orthopedics' => 1.1, 'neurology' => 1.0,
                ]),
                'priority_multipliers' => json_encode([
                    '1'=>0.80,'2'=>0.90,'3'=>1.00,'4'=>1.15,'5'=>1.30,
                ]),
                'value_cost_slope' => 0.0005,
                'is_active' => true,
            ],
            [
                'name' => 'Insurer B',
                'code' => 'INS-B',
                'notify_email' => 'ops+beta@example.com',
                'daily_capacity' => 500,
                'min_batch_size' => 3,
                'max_batch_size' => 100,
                'date_preference' => 'encounter',
                'time_cost_min' => 0.22,
                'time_cost_max' => 0.45,
                'specialty_multipliers' => json_encode([
                    'cardiology'=>1.0,'orthopedics'=>0.95,'pediatrics'=>0.88,
                ]),
                'priority_multipliers' => json_encode([
                    '1'=>0.85,'2'=>0.95,'3'=>1.00,'4'=>1.10,'5'=>1.25,
                ]),
                'value_cost_slope' => 0.0003,
                'is_active' => true,
            ],
            [
                'name' => 'Insurer C',
                'code' => 'INS-C',
                'notify_email' => 'ops+charlie@example.com',
                'daily_capacity' => 400,
                'min_batch_size' => 10,
                'max_batch_size' => 60,
                'date_preference' => 'submission',
                'time_cost_min' => 0.25,
                'time_cost_max' => 0.55,
                'specialty_multipliers' => json_encode([
                    'radiology'=>0.92,'dermatology'=>1.05,
                ]),
                'priority_multipliers' => json_encode([
                    '1'=>0.85,'2'=>0.95,'3'=>1.05,'4'=>1.20,'5'=>1.30,
                ]),
                'value_cost_slope' => 0.0004,
                'is_active' => true,
            ],
            [
                'name' => 'Insurer D',
                'code' => 'INS-D',
                'notify_email' => 'ops+delta@example.com',
                'daily_capacity' => 600,
                'min_batch_size' => 1,
                'max_batch_size' => 150,
                'date_preference' => 'encounter',
                'time_cost_min' => 0.20,
                'time_cost_max' => 0.40,
                'specialty_multipliers' => json_encode([
                    'general'=>1.0,'orthopedics'=>1.0,
                ]),
                'priority_multipliers' => json_encode([
                    '1'=>0.90,'2'=>0.95,'3'=>1.00,'4'=>1.05,'5'=>1.20,
                ]),
                'value_cost_slope' => 0.0002,
                'is_active' => true,
            ],
        ];

        DB::table('insurers')->insert($insurers);
    }
}