<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_projects')->insert([
            [
                'kode' => '2026001',
                'name' => 'Implementasi Core Banking Enhancement',
                'kategori_id' => 1,
                'bobot' => 30,
                'target' => 'Maret 2026',
                'anggaran' => 1500000000,
                'waktu' => 'TW 1',
                'tipe' => 'New',
                'pic' => 'Andi Pratama',
                'hp' => '081234567890',
                'email' => 'andi.pratama@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
