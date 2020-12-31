<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GolonganSampahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('golongan_sampahs')->insert([
            ['golongan' => 'Plastik'],
            ['golongan' => 'Kaca'],
            ['golongan' => 'Logam'],
            ['golongan' => 'Kertas']
        ]);
    }
}
