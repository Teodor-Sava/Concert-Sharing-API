<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    protected $fillable = ['user_id', 'dob', 'description', 'country_id'];

    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    public function country()
    {
        return $this->belongsTo('\App\Country');
    }
}
