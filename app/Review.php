<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['title', 'message', 'user_id', 'concert_id'];

    public function concert(){
        return $this->belongsTo('\App\Concert','concert_id');
    }

    public function user(){
        return $this->belongsTo('\App\User');
    }
}
