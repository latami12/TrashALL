<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPenyetoran extends Model
{
    protected $fillable = [
        'penyetoran_id',
        'sampah_id',
        'berat',
        'harga',
        'debit_nasabah',
    ];

    public function penyetoran()
    {
        return $this->hasOne('App\Penyetoran');
    }

    public function sampah()
    {
        return $this->hasOne('App\Sampah');
    }
}
