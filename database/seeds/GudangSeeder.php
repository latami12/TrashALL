<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert('INSERT INTO gudangs(sampah_id, total_berat) VALUES 
            (1, 200), (2, 300), (3, 400), (4, 500)
        ');
    }
}
