<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $now = now();

        $insurers = [
            [
                'name' => 'Insurer A',
                'code' => 'INS-A',
                'notification_email'=>"insa@example.com",
                'specialty_efficiency' => json_encode(['cardiology' => 1.0, 'orthopedics' => 1.5]),
                'daily_capacity' => 50,
                'min_batch_size' => 5,
                'max_batch_size' => 20,
                'batching_date_preference' => 'encounter',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Insurer B',
                'code' => 'INS-B',
                'notification_email'=>"insb@example.com",
                'specialty_efficiency' => json_encode(['cardiology' => 1.2, 'orthopedics' => 1.1]),
                'daily_capacity' => 40,
                'min_batch_size' => 3,
                'max_batch_size' => 15,
                'batching_date_preference' => 'submission',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Insurer C',
                'code' => 'INS-C',
                'notification_email'=>"insc@example.com",
                'specialty_efficiency' => json_encode(['cardiology' => 0.9, 'orthopedics' => 1.3]),
                'daily_capacity' => 60,
                'min_batch_size' => 4,
                'max_batch_size' => 25,
                'batching_date_preference' => 'encounter',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Insurer D',
                'code' => 'INS-D',
                'notification_email'=>"insd@example.com",
                'specialty_efficiency' => json_encode(['cardiology' => 1.5, 'orthopedics' => 1.0]),
                'daily_capacity' => 30,
                'min_batch_size' => 2,
                'max_batch_size' => 10,
                'batching_date_preference' => 'submission',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('insurers')->insert($insurers);
    }
}
