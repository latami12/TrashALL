<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $fillable = [
        'sampah_id',
        'total_berat',
    ];

    public function sampah()
    {
        return $this->hasOne('App\Sampah', 'id', 'sampah_Id');
    }
}
