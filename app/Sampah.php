<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sampah extends Model
{
    protected $fillable = [
        'golongan_sampah_id',
        'jenis_sampah',
        'contoh',
        'harga_perkilogram',
        'harga_jual_perkilogram',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->harga_jual_perkilogram = $query->harga_perkilogram + ($query->harga_perkilogram * 0.15);
        });
    }

    public function gudang()
    {
        return $this->hasOne('App\Gudang', 'sampah_id', 'id');
    }

    public function golonganSampah()
    {
        return $this->hasOne('App\GolonganSampah', 'id', 'golongan_sampah_id');
    }

    public function detail_penyetoran()
    {
        return $this->hasMany('App\DetailPenyetoran');
    }
}
