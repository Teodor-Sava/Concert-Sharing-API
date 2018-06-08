<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpaceRequest extends Model
{
    protected $fillable = ['user_id', 'concert_id', 'space_id', 'space_status', 'concert_status', 'request_message'];
}
