<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['user_id', 'concert_id', 'price'];

    public function concert()
    {
        return $this->belongsTo('\App\Concert', 'id');
    }
}
