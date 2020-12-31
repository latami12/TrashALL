<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sampahs')->insert([
            [
                'jenis_sampah' => 'Botol Aqua Plastik',
                'contoh' => NULL,
                'golongan_sampah_id' => 1,
                'harga_perkilogram' => 2000
            ],
            [
                'jenis_sampah' => 'Botol Sosro',
                'contoh' => NULL,
                'golongan_sampah_id' => 2,
                'harga_perkilogram' => 4000
            ],
            [
                'jenis_sampah' => 'Kaleng Sarden',
                'contoh' => NULL,
                'golongan_sampah_id' => 3,
                'harga_perkilogram' => 4000
            ],
            [
                'jenis_sampah' => 'Kardus Mie',
                'contoh' => NULL,
                'golongan_sampah_id' => 4,
                'harga_perkilogram' => 3000
            ]
        ]);
    }
}
