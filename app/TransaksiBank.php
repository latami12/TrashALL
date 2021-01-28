<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiBank extends Model
{
    protected $fillable = [
        'hari/tanggal',
        'pegawai_id',
        'keterangan_pengurus',
        'keterangan_transaksi',
        'transaksi_id',
        'penjualan_id',
    ];

    public function transaksi()
    {
        return $this->hasOne('App\Transaksi');
    }

    public function penjualan()
    {
        return $this->hasOne('App\Penjualan');
    }
}
