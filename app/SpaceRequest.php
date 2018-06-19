<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpaceRequest extends Model
{
    protected $fillable = ['user_id', 'concert_id', 'space_id', 'price', 'space_status', 'concert_status', 'request_message'];

    public function user()
    {
        return $this->hasOne('\App\User', 'id');
    }

    public function concert()
    {
        return $this->hasOne('\App\Concert', 'id');
    }

    public function space()
    {
        return $this->hasOne('\App\Space', 'id');
    }
}
