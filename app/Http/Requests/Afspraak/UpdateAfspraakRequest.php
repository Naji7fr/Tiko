<?php

namespace App\Http\Requests\Afspraak;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAfspraakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isMedewerker();
    }

    public function rules(): array
    {
        return [
            'behandeling_id' => ['required', 'exists:behandelingen,id'],
            'medewerker_id' => ['required', 'exists:medewerkers,id'],
            'afspraak_datum' => ['required', 'date', 'after_or_equal:today'],
            'afspraak_tijd' => ['required', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(?::([0-5]\d))?$/'],
            'opmerking' => ['nullable', 'string', 'max:225'],
        ];
    }
}
