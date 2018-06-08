<?php

namespace App;

use Illuminate\Notifications\Notifiable;
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

    public function band()
    {
        return $this->hasMany('\App\Band', 'user_id');
    }

    public function concerts()
    {
        return $this->hasManyThrough('\App\Concert', '\App\Ticket', 'user_id', 'ticket_id', 'id', 'id');
    }

    public function concertRequests()
    {
        return $this->hasMany('\App\ConcertRequest', 'user_id');
    }

    public function spaceRequests()
    {
        return $this->hasMany('\App\SpaceRequest', 'user_id');
    }

    public function review()
    {
        $this->hasMany('\App\Review','user_id');
    }
}
