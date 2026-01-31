<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_kategoris')->insert([
            [
                'name' => 'Pengadaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pengembangan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
