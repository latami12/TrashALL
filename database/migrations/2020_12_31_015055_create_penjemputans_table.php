<?php

use Carbon\Carbon;
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
        $tanggal = Carbon::now()->toDateTimeString();

        Schema::create('penjemputans', function (Blueprint $table) use ($tanggal) {
            $table->id();
            $table->date('tanggal')->default($tanggal);
            $table->foreignId('nasabah_id')->constrained('users');
            $table->unsignedBigInteger('pengurus1_id')->nullable();
            $table->enum('status', ['Menunggu', 'Berhasil', 'Diterima', 'Ditolak', 'Gagal']);
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
