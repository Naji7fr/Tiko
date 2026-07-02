<?php

namespace App\Http\Requests\Medewerker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isMedewerker();
    }

    public function rules(): array
    {
        return [
            'naam' => 'required|string|max:100',
            'categorie_id' => 'required|exists:categorieen,id',
            'ean_code' => ['required', 'string', 'regex:/^[0-9]{8,13}$/'],
            'voorraad' => 'required|integer|min:0',
            'leverancier_id' => 'required|exists:leveranciers,id',
            'prijs' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'naam.required' => 'Controleer de productgegevens',
            'categorie_id.required' => 'Controleer de productgegevens',
            'ean_code.required' => 'Controleer de productgegevens',
            'ean_code.regex' => 'Controleer de productgegevens',
            'voorraad.required' => 'Controleer de productgegevens',
            'leverancier_id.required' => 'Controleer de productgegevens',
            'categorie_id.exists' => 'Selecteer een geldige categorie.',
            'leverancier_id.exists' => 'Selecteer een geldige leverancier.',
            'voorraad.integer' => 'Voorraad moet een heel getal zijn.',
            'voorraad.min' => 'Voorraad kan niet negatief zijn.',
            'prijs.numeric' => 'Prijs moet een geldig getal zijn.',
        ];
    }
}
