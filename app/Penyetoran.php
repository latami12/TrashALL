<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penyetoran extends Model
{
    protected $fillable = [
        'tanggal',
        'nasabah_id',
        'pengurus1_id',
        'keterangan_penyetoran',
        'lokasi',
        'total_berat',
        'total_debit',
        'status',
    ];

    public function nasabah()
    {
        return $this->hasMany('App\User', 'id', 'nasabah_id');
    }

    public function pengurus_satu()
    {
        return $this->hasMany('App\User', 'id', 'pengurus1_id');
    }

    public function detail_penyetoran()
    {
        return $this->hasMany('App\DetailPenyetoran');
    }

    public function transaksi()
    {
        return $this->hasOne('App\Transaksi');
    }

    public function penjemputan()
    {
        return $this->hasOne('App\Penjemputan', 'id', 'penjemputan_id');
    }
}
