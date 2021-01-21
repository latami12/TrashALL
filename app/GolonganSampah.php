<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GolonganSampah extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public function sampah()
    {
        return $this->hasMany('App\Sampah', 'golongan_sampah_id', 'id');
    }
}
