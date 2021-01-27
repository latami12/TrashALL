<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $fillable = [
        'penjualan_id',
        'sampah_id',
        'berat',
        'harga_jual_pengepul',
        'debit_bank'
    ];

    public function penjualan()
    {
        return $this->hasOne('App\Penjulan');
    }
}
