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
        $harga_perkilogram = [5500, 3500, 3500, 3000, 3500, 2500, 3500, 2500, 3000, 1200, 4500, 3500, 2500, 500, 2500, 3500, 300, 4500, 3500, 1500];

        DB::table('sampahs')->insert([
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PP GELAS BENING BERSIH',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[0],
                'harga_jual_perkilogram' => $harga_perkilogram[0] + ($harga_perkilogram[0] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PP GELAS BENING KOTOR',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[1],
                'harga_jual_perkilogram' => $harga_perkilogram[1] + ($harga_perkilogram[1] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PP GELAS WARNA BERSIH',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[2],
                'harga_jual_perkilogram' => $harga_perkilogram[2] + ($harga_perkilogram[2] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PP GELAS WARNA KOTOR',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[3],
                'harga_jual_perkilogram' => $harga_perkilogram[3] + ($harga_perkilogram[3] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PET BENING',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[4],
                'harga_jual_perkilogram' => $harga_perkilogram[4] + ($harga_perkilogram[4] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PET KOTOR',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[5],
                'harga_jual_perkilogram' => $harga_perkilogram[5] + ($harga_perkilogram[5] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PET WARNA BERSIH',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[6],
                'harga_jual_perkilogram' => $harga_perkilogram[6] + ($harga_perkilogram[6] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PET WARNA KOTOR',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[7],
                'harga_jual_perkilogram' => $harga_perkilogram[7] + ($harga_perkilogram[7] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PLASTIK HD CAMPUR',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[8],
                'harga_jual_perkilogram' => $harga_perkilogram[8] + ($harga_perkilogram[8] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'NILES',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[9],
                'harga_jual_perkilogram' => $harga_perkilogram[9] + ($harga_perkilogram[9] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'LDPE INFUS',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[10],
                'harga_jual_perkilogram' => $harga_perkilogram[10] + ($harga_perkilogram[10] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'TUTUP GALON',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[11],
                'harga_jual_perkilogram' => $harga_perkilogram[11] + ($harga_perkilogram[11] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PLASTIK DAUN',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[12],
                'harga_jual_perkilogram' => $harga_perkilogram[12] + ($harga_perkilogram[12] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'DAMAR',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[13],
                'harga_jual_perkilogram' => $harga_perkilogram[13] + ($harga_perkilogram[13] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'DAMAR BENING',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[14],
                'harga_jual_perkilogram' => $harga_perkilogram[14] + ($harga_perkilogram[14] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'PLASTIK BLOW',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[15],
                'harga_jual_perkilogram' => $harga_perkilogram[15] + ($harga_perkilogram[15] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'KARUNG',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[16],
                'harga_jual_perkilogram' => $harga_perkilogram[16] + ($harga_perkilogram[16] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'KASET / CD',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[17],
                'harga_jual_perkilogram' => $harga_perkilogram[17] + ($harga_perkilogram[17] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'REFILL',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[18],
                'harga_jual_perkilogram' => $harga_perkilogram[18] + ($harga_perkilogram[18] * 0.15),
            ],
            [
                'golongan_sampah_id' => 1,
                'jenis_sampah' => 'KANTONG KRESEK',
                'contoh' => null,
                'harga_perkilogram' => $harga_perkilogram[19],
                'harga_jual_perkilogram' => $harga_perkilogram[19] + ($harga_perkilogram[19] * 0.15),
            ],
        ]);


        // BESI
        $besis = [
            ['BESI TEBAL', 2500],
            ['BESI TIPIS', 2000],
            ['BESI SENG, KALENG SUSU', 700],
            ['KUNINGAN', 30000],
            ['TEMBAGA', 60000],
            ['ALUMINIUM TEBAL', 15000],
            ['ALUMINIUM TIPIS', 12000],
            ['ALUMINIUM SIKU', 12000],
            ['PERUNNGU', 7000]
        ];

        foreach ($besis as $key => $value) {
            $besi_seed[] = [
                'golongan_sampah_id' => 2,
                'jenis_sampah' => $besis[$key][0],
                'contoh' => null,
                'harga_perkilogram' => $besis[$key][1],
                'harga_jual_perkilogram' => $besis[$key][1] + ($besis[$key][1] * 0.15)
            ];
        }

        DB::table('sampahs')->insert($besi_seed);


        // KERTAS
        $kertass = [
            ['KERTAS PUTIH', 1300],
            ['KERTAS CAMPUR', 500],
            ['KERTAS BURAM', 1200],
            ['KARDUS (DOS)', 1300],
            ['KERTAS SEMEN', 1000],
            ['KORAN', 1000],
            ['KARTON RAK TELOR', 900],
        ];

        foreach ($kertass as $key => $value) {
            $kertas_seed[] = [
                'golongan_sampah_id' => 3,
                'jenis_sampah' => $kertass[$key][0],
                'contoh' => null,
                'harga_perkilogram' => $kertass[$key][1],
                'harga_jual_perkilogram' => $kertass[$key][1] + ($kertass[$key][1] * 0.15)
            ];
        }

        DB::table('sampahs')->insert($kertas_seed);


        // KACA
        $kacas = [
            ['BOTOL MARKISABENSIN', 500],
            ['BOTOL KECAP', 500],
            ['BOTOL MARJAN', 500],
            ['BOTOL ANGGUR', 1000],
        ];

        foreach ($kacas as $key => $value) {
            $kaca_seed[] = [
                'golongan_sampah_id' => 4,
                'jenis_sampah' => $kacas[$key][0],
                'contoh' => null,
                'harga_perkilogram' => $kacas[$key][1],
                'harga_jual_perkilogram' => $kacas[$key][1] + ($kacas[$key][1] * 0.15)
            ];
        }

        DB::table('sampahs')->insert($kaca_seed);
    }
}
