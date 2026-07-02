<?php

namespace App\Models;

use App\Models\Medewerker\MedewerkerModel;
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
}
