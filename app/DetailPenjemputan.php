<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPenjemputan extends Model
{
    protected $fillable = [
        'sampah_id',
        'penjemputan_id',
        'berat',
        'harga_perkilogram',
        'harga'
    ];

    public function sampah()
    {
        return $this->hasOne('App\Sampah');
    }

    public function penjemputan()
    {
        return $this->hasOne('App\Penjemputan');
    }
}
