<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medewerker extends Model
{
    protected $fillable = [
        'naam', 'email', 'status', 'type'
    ];
}
