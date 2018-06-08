<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketRequest extends Model
{
    protected $fillable = ['ticket_id', 'concert_id', 'status'];
}
