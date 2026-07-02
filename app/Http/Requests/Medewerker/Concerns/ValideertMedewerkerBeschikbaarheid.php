<?php

namespace App\Http\Requests\Medewerker\Concerns;

use Illuminate\Contracts\Validation\Validator;

/** Gedeelde validatie voor weekrooster in medewerker-formulieren. */
trait ValideertMedewerkerBeschikbaarheid
{
    /** @return array<string, mixed> */
    protected function beschikbaarheidRegels(): array
    {
        $regels = ['beschikbaarheid' => 'nullable|array'];

        foreach (range(1, 7) as $dag) {
            $regels["beschikbaarheid.{$dag}.actief"] = 'nullable|in:0,1';
            $regels["beschikbaarheid.{$dag}.start"] = 'nullable|date_format:H:i';
            $regels["beschikbaarheid.{$dag}.eind"] = 'nullable|date_format:H:i';
        }

        return $regels;
    }

    protected function valideerBeschikbaarheid(Validator $validator): void
    {
        $beschikbaarheid = $this->input('beschikbaarheid');

        if (! is_array($beschikbaarheid)) {
            return;
        }

        foreach (range(1, 7) as $dag) {
            $dagData = $beschikbaarheid[$dag] ?? [];
            $actief = isset($dagData['actief']) && (string) $dagData['actief'] === '1';

            if (! $actief) {
                continue;
            }

            if (empty($dagData['start'])) {
                $validator->errors()->add(
                    "beschikbaarheid.{$dag}.start",
                    'Starttijd is verplicht voor een actieve dag.'
                );
            }

            if (empty($dagData['eind'])) {
                $validator->errors()->add(
                    "beschikbaarheid.{$dag}.eind",
                    'Eindtijd is verplicht voor een actieve dag.'
                );
            }

            if (
                ! empty($dagData['start'])
                && ! empty($dagData['eind'])
                && $dagData['eind'] <= $dagData['start']
            ) {
                $validator->errors()->add(
                    "beschikbaarheid.{$dag}.eind",
                    'Eindtijd moet na starttijd liggen.'
                );
            }
        }
    }
}
