<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Behandeling extends Model
{
    protected $table = 'behandelingen';

    /** @var list<string> */
    protected $fillable = [
        'naam',
        'duur_minuten',
        'prijs',
    ];

    public function afspraken(): HasMany
    {
        return $this->hasMany(Afspraak::class);
    }
}
