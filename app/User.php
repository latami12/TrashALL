<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsTo('App\Roles', 'role_id');
    }

    public function hasAnyRoles($roles) {
        return $this->roles()->whereIn('name', $roles)->first() ? TRUE : FALSE;
    }

    public function hasRole($role) {
        return $this->roles()->where('name', $role)->first() ? TRUE : FALSE;
    }

    public function whoHasRole($role) {
        return self::whereHas('roles', function($q) use ($role) { // whereHas didn't return null data
            $q->where('name', $role);
        });
    }

    public function penjemputan() {
        return $this->hasMany('App\Penjemputan', 'nasabah_id', 'id');
    }

    public function messages() {
        return $this->hasMany('App\Message', 'from_id', 'id');
    }
}