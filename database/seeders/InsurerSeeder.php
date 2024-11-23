<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    private $insurers = [
        ['name'=>'Insurer A', 'code'=> 'INS-A', 'processing_cost' => 2000, 'daily_capacity' => 3, 'min_batch_size' => 2, 'max_batch_size' => 10],
        ['name'=>'Insurer B', 'code'=> 'INS-B', 'processing_cost' => 3000, 'daily_capacity' => 4, 'min_batch_size' => 3, 'max_batch_size' => 10],
        ['name'=>'Insurer C', 'code'=> 'INS-C', 'processing_cost' => 4000, 'daily_capacity' => 5, 'min_batch_size' => 4, 'max_batch_size' => 10],
        ['name'=>'Insurer D', 'code'=> 'INS-D', 'processing_cost' => 5000, 'daily_capacity' => 6, 'min_batch_size' => 5, 'max_batch_size' => 10],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('insurers')->insert($this->insurers);
    }
}
