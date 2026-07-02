<?php

namespace App\Models\Medewerker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * medewerker.model — Technische log (audit / foutregistratie).
 *
 * Slaat technische gebeurtenissen op voor debugging en monitoring.
 */
class TechnischeLogModel extends Model
{
    public $timestamps = false;

    protected $table = 'technische_logs';

    /** @var list<string> */
    protected $fillable = [
        'niveau',
        'module',
        'actie',
        'bericht',
        'gebruiker_id',
        'ip_adres',
        'created_at',
    ];

    /**
     * Schrijft een technische logregel (database + Laravel log).
     */
    public static function registreer(
        string $niveau,
        string $module,
        string $actie,
        string $bericht
    ): void {
        $logData = [
            'niveau' => $niveau,
            'module' => $module,
            'actie' => $actie,
            'bericht' => $bericht,
            'gebruiker_id' => Auth::id(),
            'ip_adres' => Request::ip(),
            'created_at' => now(),
        ];

        try {
            static::create($logData);
        } catch (\Throwable) {
            // Tabel kan ontbreken in testomgeving; Laravel-log blijft werken.
        }

        Log::channel('single')->{$niveau === 'error' ? 'error' : 'info'}(
            "[{$module}] {$actie}: {$bericht}",
            ['gebruiker_id' => Auth::id(), 'ip' => Request::ip()]
        );
    }
}
