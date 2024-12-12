<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    private $insurers = [
     [
         'email' => 'InsurerA@gmail.com',
         'name' => 'Insurer A',
         'code' => 'INS-A',
         'batch_preference' => 'submission_date',
         'specialty_efficiency' => ['cardiology' => 85, 'orthopedics' => 90], // Example JSON
     ],
     [
         'email' => 'InsurerB@gmail.com',
         'name' => 'Insurer B',
         'code' => 'INS-B',
         'batch_preference' => 'encounter_date',
         'specialty_efficiency' => ['cardiology' => 80, 'orthopedics' => 85],
     ],
     [
         'email' => 'InsurerC@gmail.com',
         'name' => 'Insurer C',
         'code' => 'INS-C',
         'batch_preference' => 'submission_date',
         'specialty_efficiency' => ['cardiology' => 70, 'orthopedics' => 75],

     ],
     [
         'email' => 'InsurerD@gmail.com',
         'name' => 'Insurer D',
         'code' => 'INS-D',
         'batch_preference' => 'encounter_date',
         'specialty_efficiency' => ['cardiology' => 90, 'orthopedics' => 95],
     ],
 ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('insurers')->truncate();
        $now = Carbon::now();

        $insurers = array_map(function ($insurer) use ($now) {
            return [
                'name' => $insurer['name'],
                'email' => $insurer['email'],
                'code' => $insurer['code'],
                'batch_preference' => $insurer['batch_preference'],
                'specialty_efficiency' => json_encode($insurer['specialty_efficiency']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $this->insurers);

        DB::table('insurers')->insert($insurers);
    }
}
