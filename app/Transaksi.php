<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'tanggal',
        'nasabah_id',
        'keterangan_transaksi',
        'penyetoran_id',
        'debit',
        'kredit',
    ];

    public function nasabah()
    {
        return $this->hasMany('App\User');
    }

    public function penyetoran()
    {
        return $this->hasOne('App\Penyetoran');
    }
}
