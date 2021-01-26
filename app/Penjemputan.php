<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjemputan extends Model
{
    protected $fillable = [
        'tanggal',
        'nasabah_id',
        'pengurus1_id',
        'status',
        'lokasi',
        'total_berat',
        'total_harga',
        'image',
    ];

    public function nasabah()
    {
        return $this->hasOne('App\User', 'id', 'nasabah_id');
    }

    public function pengurus_satu()
    {
        return $this->hasOne('App\User', 'id', 'pengurus1_id');
    }

    public function detail_penjemputan()
    {
        return $this->hasMany('App\DetailPenjemputan');
    }

    public function penyetoran()
    {
        return $this->hasOne('App\Penyetoran', 'id', 'penjemputan_id');
    }
}
