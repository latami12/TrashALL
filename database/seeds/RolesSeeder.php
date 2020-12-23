<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['role' => 'Nasabah'],
            ['role' => 'Pengurus 1'],
            ['role' => 'Pengurus 2'],
            ['role' => 'Bendahara'],
            ['role' => 'Admin']
        ]);
    }
}
