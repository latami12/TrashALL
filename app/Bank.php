<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'total_sampah_masuk',
        'total_sampah_keluar',
        'total_debit_nasabah',
        'total_kredit_nasabah',
        'total_penjualan_ke_pengepul',
        'total_saldo',
    ];
}
