<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['type'];

    protected $hidden = ['created_at', 'updated_at'];

    public function band()
    {
        return $this->belongsToMany('\App\Band', 'band_genres');
    }
}
