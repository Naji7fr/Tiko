<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reis extends Model
{
    protected $table = 'reizen';

    protected $fillable = [
        'titel',
        'bestemming',
        'start_datum',
        'eind_datum',
        'status',
        'beschrijving',
        'prijs',
    ];

    protected $casts = [
        'start_datum' => 'date',
        'eind_datum' => 'date',
        'prijs' => 'decimal:2',
    ];
}
