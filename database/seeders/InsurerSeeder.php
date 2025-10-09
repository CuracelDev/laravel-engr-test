<?php

namespace Database\Seeders;

use App\Models\Insurer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsurerSeeder extends Seeder
{
    private $insurers = [
        ['name'=>'Insurer A', 'code'=> 'INS-A'],
        ['name'=>'Insurer B', 'code'=> 'INS-B'],
        ['name'=>'Insurer C', 'code'=> 'INS-C'],
        ['name'=>'Insurer D', 'code'=> 'INS-D'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Insurer::create([
            'name' => 'Insurer A',
            'code' => 'INS001',
            'min_batch_size' => 2,
            'max_batch_size' => 10,
            'processing_costs' => json_encode([
                'day_1_to_10' => 0.2,
                'day_11_to_20' => 0.3,
                'day_21_to_30' => 0.4,
            ]),
        ]);

        Insurer::create([
            'name' => 'Insurer B',
            'code' => 'INS002',
            'min_batch_size' => 5,
            'max_batch_size' => 15,
            'processing_costs' => json_encode([
                'day_1_to_10' => 0.25,
                'day_11_to_20' => 0.35,
                'day_21_to_30' => 0.45,
            ]),
        ]);
    }
}
