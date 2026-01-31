<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_projects')->insert([
            [
                'tahapan_id' => 1,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 100,
                'nilai' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 2,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 100,
                'nilai' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 3,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 4,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 5,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 6,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 7,
                'project_id' => 1,
                'bobot' => 5,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 8,
                'project_id' => 1,
                'bobot' => 15,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 9,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahapan_id' => 10,
                'project_id' => 1,
                'bobot' => 10,
                'progres' => 0,
                'nilai' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
