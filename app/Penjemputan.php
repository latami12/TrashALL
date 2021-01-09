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
        return $this->hasOne('App\User');
    }

    public function pengurus_satu()
    {
        return $this->hasOne('App\User');
    }

    public function detail_penjemputan()
    {
        return $this->hasMany('App\DetailPenjemputan');
    }
}
