<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $fillable = ['name', 'band_id', 'space_id', 'available_tickets', 'total_tickets', 'concert_start', 'user_id', 'short_description', 'long_description'];

    public function band()
    {
        return $this->hasOne('\App\Band', 'id');
    }

    public function space()
    {
        return $this->hasOne('\App\Space', 'id');
    }

    public function user()
    {
        return $this->belongsTo('\App\User', 'id');
    }
}
