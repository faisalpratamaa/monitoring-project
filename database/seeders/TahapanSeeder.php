<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahapanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tahapans')->insert([
            [
                'kode' => 'THP001',
                'kategori_id' => 1,
                'name' => 'Studi Kelayakan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP002',
                'kategori_id' => 1,
                'name' => 'RFI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP003',
                'kategori_id' => 1,
                'name' => 'Ijin Prinsip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP004',
                'kategori_id' => 1,
                'name' => 'HPS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP005',
                'kategori_id' => 1,
                'name' => 'Permohonan Pengadaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP006',
                'kategori_id' => 1,
                'name' => 'Proses Pengadaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP007',
                'kategori_id' => 1,
                'name' => 'Kontrak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP008',
                'kategori_id' => 1,
                'name' => 'Pengiriman Barang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP009',
                'kategori_id' => 1,
                'name' => 'Implementasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'THP010',
                'kategori_id' => 1,
                'name' => 'Go Live',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}