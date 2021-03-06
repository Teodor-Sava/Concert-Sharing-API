<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Band extends Model
{
    protected $fillable = ['name', 'country_id', 'no_members', 'founded_at', 'band_requests', 'price', 'short_description', 'long_description', 'image_url', 'user_id'];


    public function country()
    {
        return $this->belongsTo('\App\Country');
    }

    public function genre()
    {
        return $this->belongsToMany('\App\Genre', 'band_genres', 'band_id', 'genre_id');
    }

    public function concert()
    {
        return $this->hasMany('\App\Concert', 'concert_id');
    }

    public function user()
    {
        return $this->belongsTo('\App\Concert', 'user_id');
    }

    public function concert_request()
    {
        return $this->hasMany('\App\ConcertRequest','band_id');
    }
}
