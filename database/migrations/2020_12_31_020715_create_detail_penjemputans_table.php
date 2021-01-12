<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPenjemputansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_penjemputans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjemputan_id')->constrained('penjemputans');
            $table->foreignId('sampah_id')->constrained('sampahs');
            $table->decimal('berat');
            $table->decimal('harga_perkilogram');
            $table->decimal('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_penjemputans');
    }
}
