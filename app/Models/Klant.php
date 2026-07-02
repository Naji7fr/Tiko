<?php

namespace App\Models;

use App\Models\Medewerker\GebruikerModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Klant (klantaccount in de barbershop).
 *
 * Koppelt een Laravel User (login) aan een Gebruiker-record (persoonsgegevens).
 * ERD: users ← klanten → gebruikers → contact_gegevens
 */
class Klant extends Model
{
    protected $table = 'klanten';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'gebruiker_id',
    ];

    /** Login-account van deze klant. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Persoonsgegevens (naam, contact via contact_gegevens). */
    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(GebruikerModel::class, 'gebruiker_id');
    }
}
