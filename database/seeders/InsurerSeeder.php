<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    private $insurers = [
        ['name' => 'Insurer A', 'code' => 'INS-A'],
        ['name' => 'Insurer B', 'code' => 'INS-B'],
        ['name' => 'Insurer C', 'code' => 'INS-C'],
        ['name' => 'Insurer D', 'code' => 'INS-D'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('insurers')->insert($this->insurers);
    }
}
