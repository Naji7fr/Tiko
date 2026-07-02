<?php

namespace App\Http\Requests\Medewerker;

use App\Models\Medewerker\MedewerkerModel;
use Illuminate\Foundation\Http\FormRequest;

/**
 * medewerker.request — Server-side validatie bij wijzigen medewerker.
 */
class UpdateMedewerkerRequest extends FormRequest
{
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

        if ($medewerker instanceof MedewerkerModel) {
            $contactId = $medewerker->gebruiker()->value('contact_gegevens_id');
        }

        $emailRule = 'required|email|max:254|unique:contact_gegevens,email';
        if ($contactId) {
            $emailRule .= ',' . $contactId;
        }

        return [
            'voornaam' => 'required|string|max:50',
            'tussenvoegsel' => 'nullable|string|max:20',
            'achternaam' => 'required|string|max:50',
            'email' => $emailRule,
            'telefoon' => ['nullable', 'regex:/^(?:\+31|0031|0)[1-9][0-9]{8}$/'],
            'specialisatie_id' => 'required|exists:specialisaties,id',
            'is_actief' => 'required|in:1,0',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.unique' => 'Deze e-mail bestaat al.',
            'telefoon.regex' => 'Ongeldig telefoonnummer.',
        ];
    }
}
