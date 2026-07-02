<?php

namespace App\Models;

use App\Models\Medewerker\MedewerkerModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Afspraak extends Model
{
    protected $table = 'afspraken';

    /** @var list<string> */
    protected $fillable = [
        'klant_id',
        'medewerker_id',
        'behandeling_id',
        'afspraak_datum',
        'afspraak_tijd',
        'opmerking',
    ];

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class);
    }

    public function medewerker(): BelongsTo
    {
        return $this->belongsTo(MedewerkerModel::class);
    }

    public function behandeling(): BelongsTo
    {
        return $this->belongsTo(Behandeling::class);
    }

    public function startDateTime(): Carbon
    {
        return Carbon::parse("{$this->afspraak_datum} {$this->afspraak_tijd}");
    }

    public function eindDateTime(): Carbon
    {
        $duurMinuten = $this->behandeling?->duur_minuten ?? 30;

        return $this->startDateTime()->copy()->addMinutes($duurMinuten);
    }

    /** Afspraak op een eerdere dag (niet meer annuleerbaar). */
    public function isVerstreken(): bool
    {
        return $this->afspraak_datum < now()->toDateString();
    }

    /** Afspraak is vandaag begonnen en duurt nog (behandeling loopt). */
    public function isLopend(): bool
    {
        if ($this->afspraak_datum !== now()->toDateString()) {
            return false;
        }

        $now = now();

        return $now->gte($this->startDateTime()) && $now->lt($this->eindDateTime());
    }
}
