<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPenyetoransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_penyetorans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyetoran_id')->constrained('penyetorans');
            $table->foreignId('sampah_id')->constrained('sampahs');
            $table->decimal('berat', 8, 2);
            $table->decimal('harga', 10, 2);
            $table->decimal('debit_nasabah', 10, 2);
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
        Schema::dropIfExists('detail_penyetorans');
    }
}
