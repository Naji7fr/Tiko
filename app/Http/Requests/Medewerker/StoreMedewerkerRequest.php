<?php

namespace App\Http\Requests\Medewerker;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * medewerker.request — Server-side validatie bij aanmaken medewerker.
 *
 * Security: geautoriseerde input, whitelist velden, regex telefoon.
 */
class StoreMedewerkerRequest extends FormRequest
{
    /** Alleen admin/medewerker mogen medewerkers aanmaken (route-middleware). */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /** Validatieregels voor nieuwe medewerker (whitelist velden). */
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'voornaam' => 'required|string|max:50',
            'tussenvoegsel' => 'nullable|string|max:20',
            'achternaam' => 'required|string|max:50',
            'email' => 'required|email|max:254|unique:contact_gegevens,email',
            'telefoon' => [
                'nullable',
                'regex:/^(?:\+31|0031|0)[1-9][0-9]{8}$/',
                Rule::unique('contact_gegevens', 'telefoon'),
            ],
            'specialisatie_id' => 'required|exists:specialisaties,id',
            'is_actief' => 'required|in:1,0',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.unique' => 'Deze e-mail bestaat al.',
            'telefoon.unique' => 'Dit telefoonnummer bestaat al.',
            'telefoon.regex' => 'Ongeldig telefoonnummer.',
            'voornaam.required' => 'Voornaam is verplicht.',
            'achternaam.required' => 'Achternaam is verplicht.',
            'specialisatie_id.required' => 'Selecteer een specialisatie.',
        ];
    }
}
