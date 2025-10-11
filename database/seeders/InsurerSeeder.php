<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    private $insurers = [
        ['name'=>'Insurer A', 'code'=> 'INS-A', 'email'=>'issurer1@email.com' ],
        ['name'=>'Insurer B', 'code'=> 'INS-B', 'email'=>'issurer2@email.com' ],
        ['name'=>'Insurer C', 'code'=> 'INS-C', 'email'=>'issurer3@email.com' ],
        ['name'=>'Insurer D', 'code'=> 'INS-D', 'email'=>'issurer4@email.com' ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('insurers')->insert($this->insurers);
    }
}
