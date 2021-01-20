<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenjemputansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjemputans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('nasabah_id')->constrained('users');
            $table->unsignedBigInteger('pengurus1_id')->nullable();
            $table->enum('status', ['Menunggu', 'Berhasil', 'Diterima', 'Ditolak']);
            $table->text('lokasi')->nullable();
            $table->decimal('total_berat', 8, 2)->nullable();
            $table->decimal('total_harga', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('pengurus1_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penjemputans');
    }
}
