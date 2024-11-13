<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HmoSeeder extends Seeder
{
    private $hmos = [
        ['name'=>'HMO A', 'email' =>'test@gmail.com', 'code'=> 'HMO-A'],
        ['name'=>'HMO B', 'email' =>'test1@gmail.com', 'code'=> 'HMO-B'],
        ['name'=>'HMO C', 'email' =>'test2@gmail.com', 'code'=> 'HMO-C'],
        ['name'=>'HMO D', 'email' =>'test3@gmail.com', 'code'=> 'HMO-D'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hmos')->insert($this->hmos);
    }
}
