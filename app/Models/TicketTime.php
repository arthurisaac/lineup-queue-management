<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'passage',
        'user',
        'agence',
    ] ;

    protected $casts = [
        'passage' => 'date:Y-m-d H:i'
    ];
}
