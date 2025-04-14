<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insurers = [
            [
                'name' => 'Insurer A',
                'code' => 'INS-A',
                'user_id' => User::factory()->create([
                    'name' => 'Insurer A User',
                    'email' => 'insurer.a@example.com',
                ])->id,
                'speciality' => 'cardiology',
            ],
            [
                'name' => 'Insurer B',
                'code' => 'INS-B',
                'user_id' => User::factory()->create([
                    'name' => 'Insurer B User',
                    'email' => 'insurer.b@example.com',
                ])->id,
                'speciality' => 'orthopedics',
            ],
            [
                'name' => 'Insurer C',
                'code' => 'INS-C',
                'user_id' => User::factory()->create([
                    'name' => 'Insurer C User',
                    'email' => 'insurer.c@example.com',
                ])->id,
                'speciality' => 'neurology',
            ],
            [
                'name' => 'Insurer D',
                'code' => 'INS-D',
                'user_id' => User::factory()->create([
                    'name' => 'Insurer D User',
                    'email' => 'insurer.d@example.com',
                ])->id,
                'speciality' => 'oncology',
            ],
        ];

        DB::table('insurers')->insert($insurers);
    }
}
