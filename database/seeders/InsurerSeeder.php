<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    private $insurers = [
        ['name'=>'Insurer A', 'code'=> 'INS-A', 'daily_limits' => 100, 'total_processing_cost' => '19239232', 'date_preference' => 'encounter'],
        ['name'=>'Insurer B', 'code'=> 'INS-B', 'daily_limits' => 12, 'total_processing_cost' => '10292933', 'date_preference' => 'submission'],
        ['name'=>'Insurer C', 'code'=> 'INS-C', 'daily_limits' => 23, 'total_processing_cost' => '10292929', 'date_preference' => 'encounter'],
        ['name'=>'Insurer D', 'code'=> 'INS-D', 'daily_limits' => 3, 'total_processing_cost' => '1029445', 'date_preference' => 'submission'],
        ['name'=>'Insurer E', 'code'=> 'INS-E', 'daily_limits' => 3, 'total_processing_cost' => '102045', 'date_preference' => 'encounter'],
        ['name'=>'Insurer F', 'code'=> 'INS-F', 'daily_limits' => 14, 'total_processing_cost' => '1024', 'date_preference' => 'submission'],
        ['name'=>'Insurer G', 'code'=> 'INS-G', 'daily_limits' => 6, 'total_processing_cost' => '102933', 'date_preference' => 'submission'],
        ['name'=>'Insurer H', 'code'=> 'INS-H', 'daily_limits' => 7, 'total_processing_cost' => '102333', 'date_preference' => 'submission'],
        ['name'=>'Insurer I', 'code'=> 'INS-I', 'daily_limits' => 17, 'total_processing_cost' => '1433', 'date_preference' => 'encounter'],
        ['name'=>'Insurer J', 'code'=> 'INS-J', 'daily_limits' => 9, 'total_processing_cost' => '19004', 'date_preference' => 'submission'],
        ['name'=>'Insurer K', 'code'=> 'INS-K', 'daily_limits' => 9, 'total_processing_cost' => '1046567', 'date_preference' => 'encounter'],
        ['name'=>'Insurer L', 'code'=> 'INS-L', 'daily_limits' => 11, 'total_processing_cost' => '102242', 'date_preference' => 'submission'],
        ['name'=>'Insurer M', 'code'=> 'INS-M', 'daily_limits' => 17, 'total_processing_cost' =>'102233', 'date_preference' => 'encounter'],
        ['name'=>'Insurer N', 'code'=> 'INS-N', 'daily_limits' => 33, 'total_processing_cost' => '1029299', 'date_preference' => 'submission'],
        ['name'=>'Insurer O', 'code'=> 'INS-O', 'daily_limits' => 9, 'total_processing_cost' => '10292943', 'date_preference' => 'submission'],
        ['name'=>'Insurer P', 'code'=> 'INS-P', 'daily_limits' => 15, 'total_processing_cost' => '10292929', 'date_preference' => 'encounter'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->insurers as $insurer) {
            $insurer['maximum_num_of_batch'] = rand(5,10);
            $insurer['minimum_num_of_batch'] = rand(2,4);
            $insurer['created_at'] = now();
            $insurer['updated_at'] = now();
            DB::table('insurers')->updateOrInsert(
                ['code' => $insurer['code']],
                $insurer
            );
        }
    }
}
