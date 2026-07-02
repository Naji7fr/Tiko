<?php

namespace App\Http\Requests\Medewerker;

use App\Http\Requests\Medewerker\Concerns\ValideertMedewerkerBeschikbaarheid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * medewerker.request — Server-side validatie bij aanmaken medewerker.
 *
 * Security: geautoriseerde input, whitelist velden, regex telefoon.
 */
class StoreMedewerkerRequest extends FormRequest
{
    use ValideertMedewerkerBeschikbaarheid;

    /** Alleen eigenaar mag medewerkers aanmaken. */
    public function authorize(): bool
    {
        return $this->user()?->isEigenaar() ?? false;
    }

    /** Validatieregels voor nieuwe medewerker (whitelist velden). */
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge([
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
        ], $this->beschikbaarheidRegels());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->valideerBeschikbaarheid($validator);
        });
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
