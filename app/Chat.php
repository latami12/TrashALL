<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [ 'from', 'to', 'chat', 'is_read'];

    public function profile()
    {
        return $this->hasOne('App\User');
    }
}
