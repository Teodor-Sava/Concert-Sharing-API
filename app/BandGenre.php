<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BandGenre extends Model
{
    protected $fillable = ['band_id', 'genre_id'];

    public $timestamps = false;
}
