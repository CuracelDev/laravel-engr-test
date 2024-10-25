<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HmoSeeder extends Seeder
{
    private $hmos = [
        ['name'=>'HMO A', 'code'=> 'HMO-A', 'email' => 'a@mail.com', 'batches_type' => 'actual'],
        ['name'=>'HMO B', 'code'=> 'HMO-B', 'email' => 'b@mail.com', 'batches_type' => 'encounter'],
        ['name'=>'HMO C', 'code'=> 'HMO-C', 'email' => 'c@mail.com', 'batches_type' => 'encounter'],
        ['name'=>'HMO D', 'code'=> 'HMO-D', 'email' => 'd@mail.com', 'batches_type' => 'encounter'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hmos')->insert($this->hmos);
    }
}
