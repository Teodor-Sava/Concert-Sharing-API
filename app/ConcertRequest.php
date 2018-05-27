<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConcertRequest extends Model
{
    protected $fillable = ['user_id', 'concert_id', 'band_id', 'status'];

    public function user()
    {
        $this->hasOne('\App\User', 'id');
    }

    public function concert()
    {
        $this->hasOne('\App\Concert', 'id');
    }

    public function band()
    {
        $this->hasOne('\App\Band', 'id');
    }
}
