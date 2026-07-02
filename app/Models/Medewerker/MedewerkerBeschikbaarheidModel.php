<?php

namespace App\Models\Medewerker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * medewerker.model — Weekrooster per medewerker (1 rij per dag).
 */
class MedewerkerBeschikbaarheidModel extends Model
{
    protected $table = 'medewerker_beschikbaarheid';

    /** @var list<string> */
    protected $fillable = [
        'medewerker_id',
        'dag_van_week',
        'start_tijd',
        'eind_tijd',
        'is_beschikbaar',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'dag_van_week' => 'integer',
            'is_beschikbaar' => 'boolean',
        ];
    }

    public function medewerker(): BelongsTo
    {
        return $this->belongsTo(MedewerkerModel::class, 'medewerker_id');
    }
}
