<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Insurer;
use App\Models\InsurerSpecialtyEfficiency;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insurers_ids = Insurer::select('id')->distinct()->pluck('id');
        $specialties = collect();

        foreach ($insurers_ids as $insurer_id) {
            // Fetch the first 3 records for the current insurer
            $specialties = $specialties->merge(
                InsurerSpecialtyEfficiency::where('insurers_id', $insurer_id)
                    ->take(3)
                    ->get()
            );
        }

        foreach($specialties as $key => $specialty){
            Claim::factory(3)->create([
                'specialty_id' => $specialty->specialty_id, 
                'insurer_code' => $specialty->insurers_id, 
                'efficiency_id' =>  $specialty->id
            ])->each(function ($claim) {
                $itemsCount = rand(2, 6); 
                
                Item::factory($itemsCount)->create([
                    'claim_id' => $claim->id, 
                ]);
            });
        }
    }
}
