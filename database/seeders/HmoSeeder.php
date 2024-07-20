<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HmoSeeder extends Seeder
{
    private $hmos = [
        ['name'=>'HMO A', 'code'=> 'HMO-A', 'email' => 'hmoa@email.com', 'batching_criteria' => 'submission_date'],
        ['name'=>'HMO B', 'code'=> 'HMO-B', 'email' => 'hmob@email.com', 'batching_criteria' => 'encounter_date'],
        ['name'=>'HMO C', 'code'=> 'HMO-C', 'email' => 'hmoc@email.com', 'batching_criteria' => 'submission_date'],
        ['name'=>'HMO D', 'code'=> 'HMO-D', 'email' => 'hmod@email.com', 'batching_criteria' => 'encounter_date'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hmos')->insert($this->hmos);
    }
}
