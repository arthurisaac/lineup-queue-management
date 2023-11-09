<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero',
        'service',
        'service_id',
        'served',
        'etat',
        'guichet',
        'code',
    ] ;

    public function Service() {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
