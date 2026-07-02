<?php

namespace App\Http\Requests\Medewerker;

use App\Models\Medewerker\MedewerkerModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * medewerker.request — Server-side validatie bij wijzigen medewerker.
 *
 * E-mail unique-regel sluit het huidige contactrecord uit (update, geen duplicate check op zichzelf).
 */
class UpdateMedewerkerRequest extends FormRequest
{
    /** Alleen admin/medewerker mogen medewerkers wijzigen. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
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

        return [
            'voornaam' => 'required|string|max:50',
            'tussenvoegsel' => 'nullable|string|max:20',
            'achternaam' => 'required|string|max:50',
            'email' => $emailRule,
            'telefoon' => $telefoonRules,
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
        ];
    }
}
