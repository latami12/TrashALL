<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TabunganUser extends Model
{
    protected $fillable = [
        'nasabah_id',
        'transaksi_id',
        'hari/tanggal',
        'keterangan',
        'jenis_sampah',
        'berat',
        'debit',
        'kredit',
        'saldo',
    ];

    public function transaksi()
    {
        return $this->hasMany('App\Transaksi');
    }
}
