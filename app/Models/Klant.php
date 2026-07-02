<?php

namespace App\Models;

use App\Models\Medewerker\GebruikerModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Klant (customer account — login via User + Gebruiker).
 */
class Klant extends Model
{
    protected $table = 'klanten';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'gebruiker_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(GebruikerModel::class, 'gebruiker_id');
    }
}
