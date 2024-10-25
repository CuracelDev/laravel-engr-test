<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    private $hmos = [
        ['name'=>'Provider A'],
        ['name'=>'Provider B'],
        ['name'=>'Provider C'],
        ['name'=>'Provider D'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('providers')->insert($this->hmos);
    }
}
