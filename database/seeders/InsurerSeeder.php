<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Insurer;

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
    public function run()
    {
        foreach ($this->insurers as $insurer) {
            Insurer::create([
                'code' => $insurer['code'],
                'name' => $insurer['name'],
                'daily_capacity' => 1000, 
                'min_batch_size' => 10,   
                'max_batch_size' => 100,  
                'preferred_date_type' => 'encounter', 
                'specialty_efficiency' => json_encode([
                    'cardiology' => 1.0, 
                    'orthopedics' => 1.0,
                ]),
            ]);
    }
}
}
