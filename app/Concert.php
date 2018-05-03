<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $fillable = ['name', 'band_id', 'space_id', 'available_tickets', 'total_tickets', 'concert_start'];
}
