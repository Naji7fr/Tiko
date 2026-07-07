<?php

namespace App\Http\Requests\Medewerker;

use App\Http\Requests\Medewerker\Concerns\ValideertMedewerkerBeschikbaarheid;
use App\Models\Medewerker\MedewerkerModel;
use App\Support\ValidatieRegels;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * medewerker.request — Server-side validatie bij wijzigen medewerker.
 *
 * E-mail unique-regel sluit het huidige contactrecord uit (update, geen duplicate check op zichzelf).
 */
class UpdateMedewerkerRequest extends FormRequest
{
    use ValideertMedewerkerBeschikbaarheid;

    /** Alleen eigenaar mag medewerkers wijzigen. */
    public function authorize(): bool
    {
        return $this->user()?->isEigenaar() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var MedewerkerModel|null $medewerker */
        $medewerker = $this->route('medewerker');
        $contactId = null;

        // Huidig contact_gegevens_id ophalen voor unique:email uitzondering
        if ($medewerker instanceof MedewerkerModel) {
            $contactId = $medewerker->gebruiker()->value('contact_gegevens_id');
        }

        $emailRule = 'required|email|max:254|unique:contact_gegevens,email';
        if ($contactId) {
            $emailRule .= ',' . $contactId;
        }

        $telefoonRules = [
            'nullable',
            'regex:/^(?:\+31|0031|0)[1-9][0-9]{8}$/',
            Rule::unique('contact_gegevens', 'telefoon')->ignore($contactId),
        ];

        return array_merge([
            'voornaam' => ValidatieRegels::voornaam(),
            'tussenvoegsel' => ValidatieRegels::tussenvoegsel(),
            'achternaam' => ValidatieRegels::achternaam(),
            'email' => $emailRule,
            'telefoon' => $telefoonRules,
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
        return array_merge(
            ValidatieRegels::naamBerichten(),
            ValidatieRegels::emailBerichten(),
            [
                'email.unique' => 'Deze e-mail bestaat al.',
                'telefoon.unique' => 'Dit telefoonnummer bestaat al.',
                'telefoon.regex' => 'Ongeldig telefoonnummer.',
            ]
        );
    }
}
