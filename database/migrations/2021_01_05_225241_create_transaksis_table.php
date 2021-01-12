<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('nasabah_id')->constrained('users');
            $table->enum('keterangan_transaksi', ['diantar', 'dijemput', 'penarikan']);
            $table->unsignedBigInteger('penyetoran_id')->nullable();
            $table->decimal('debit', 10, 2)->nullable();
            $table->decimal('kredit', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('penyetoran_id')->references('id')->on('penyetorans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
}
