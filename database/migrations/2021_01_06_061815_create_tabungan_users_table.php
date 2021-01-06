<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabunganUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabungan_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('users');
            $table->foreignId('transaksi_id')->constrained('transaksis');
            $table->date('hari/tanggal');
            $table->enum('keterangan', ['diantar', 'dijemput', 'penarikan']);
            $table->string('jenis_sampah')->nullable();
            $table->decimal('berat', 8, 2);
            $table->decimal('debit', 10, 2);
            $table->decimal('kredit', 10, 2);
            $table->decimal('saldo', 10, 2);
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
        Schema::dropIfExists('tabungan_users');
    }
}
