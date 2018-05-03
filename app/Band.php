<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Band extends Model
{
    protected $fillable = ['name', 'country_id', 'no_members', 'founded_at', 'band_requests'];
}
