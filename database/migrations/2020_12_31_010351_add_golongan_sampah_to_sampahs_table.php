<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGolonganSampahToSampahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sampahs', function (Blueprint $table) {
            $table->unsignedBigInteger('golongan_sampah_id')->after('contoh');
            $table->foreign('golongan_sampah_id')->references('id')->on('golongan_sampahs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sampahs', function (Blueprint $table) {
            $table->dropForeign('golongan_sampah_id');
        });
    }
}
