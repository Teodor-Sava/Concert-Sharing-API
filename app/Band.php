<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Band extends Model
{
    use Searchable;

    protected $fillable = ['name', 'country_id', 'no_members', 'founded_at', 'band_requests'];


    public function searchableAs()
    {
        return 'name';
    }

    public function country()
    {
        return $this->belongsTo('\App\Country');
    }

    public function genre(){
        return $this->belongsToMany('\App\Genre','band_genres');
    }
}
