<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    use HasFactory;

    protected $fillable = [
        'guichet',
        'service',
        'ticket',
    ] ;

    public function Service() {
        return $this->belongsTo(Service::class,'service');
    }

    public function Ticket() {
        return $this->belongsTo(Ticket::class,'ticket');
    }
}
