<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteBands extends Model
{
    protected $fillable = ['user_id', 'band_id'];
}
