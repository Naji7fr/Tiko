<?php

namespace App\Http\Requests\Eigenaar;

use App\Models\Medewerker\ContactGegevensModel;
use App\Services\Eigenaar\AccountService;
use App\Support\ValidatieRegels;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * account.request — Validatie bij wijzigen account.
 */
class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isEigenaar() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var \App\Models\User|null $account */
        $account = $this->route('account');

        return [
            'voornaam' => ValidatieRegels::voornaam(),
            'achternaam' => ValidatieRegels::achternaam(),
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($account?->id),
            ],
            'telefoon' => ['nullable', 'string', 'max:25', 'regex:/^(?:\+31|0031|0)[1-9][0-9]{8}$/'],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(AccountService::BEHEER_ROLLEN)],
            'status' => ['required', Rule::in(['Actief', 'Inactief'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('role') !== 'klant') {
                return;
            }

            /** @var \App\Models\User|null $account */
            $account = $this->route('account');
            $contactId = $account?->klant?->gebruiker?->contact_gegevens_id;

            $exists = ContactGegevensModel::query()
                ->where('email', $this->input('email'))
                ->when($contactId, fn ($query) => $query->where('id', '!=', $contactId))
                ->exists();

            if ($exists) {
                $validator->errors()->add('email', 'Dit e-mailadres is al in gebruik.');
            }
        });
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return array_merge(
            ValidatieRegels::naamBerichten(),
            ValidatieRegels::emailBerichten(),
            [
                'password.min' => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
                'password.confirmed' => 'De wachtwoorden komen niet overeen.',
                'telefoon.regex' => 'Ongeldig telefoonnummer.',
            ]
        );
    }
}
