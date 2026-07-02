<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

/**
 * Model: User (login-account voor beheerders én klanten).
 *
 * - admin / medewerker → medewerkerbeheer
 * - klant           → klant-portaal (dashboard, later afspraken)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> Mass assignment: login- en profielvelden */
    protected $fillable = [
        'name',
        'voornaam',
        'achternaam',
        'email',
        'profile_photo_path',
        'password',
        'role',
        'status',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Controleer of de gebruiker beheerrechten heeft (admin of medewerker). */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'medewerker'], true);
    }

    /** Controleer of de gebruiker de eigenaar is (volledige toegang). */
    public function isEigenaar(): bool
    {
        return $this->role === 'admin';
    }

    /** Controleer of de gebruiker een medewerker-account is (geen eigenaar). */
    public function isMedewerker(): bool
    {
        return $this->role === 'medewerker';
    }

    /** Controleer of de gebruiker een klant is. */
    public function isKlant(): bool
    {
        return $this->role === 'klant';
    }

    /** Gekoppeld klantprofiel (alleen voor role=klant). */
    public function klant(): HasOne
    {
        return $this->hasOne(Klant::class);
    }

    /** Bepaalt de zichtbare profielfoto of gebruikt een placeholder. */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? Storage::disk('public')->url($this->profile_photo_path)
            : asset('images/profile-placeholder.svg');
    }
}
