<?php

namespace App\Http\Requests\Medewerker;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'ean_code' => 'required|string|max:13',
            'voorraad' => 'required|integer|min:0',
            'leverancier_id' => 'required|exists:leveranciers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'naam.required' => 'Vul alle verplichte productgegevens in',
            'categorie_id.required' => 'Vul alle verplichte productgegevens in',
            'ean_code.required' => 'Vul alle verplichte productgegevens in',
            'voorraad.required' => 'Vul alle verplichte productgegevens in',
            'leverancier_id.required' => 'Vul alle verplichte productgegevens in',
            'categorie_id.exists' => 'Selecteer een geldige categorie.',
            'leverancier_id.exists' => 'Selecteer een geldige leverancier.',
            'voorraad.integer' => 'Voorraad moet een heel getal zijn.',
            'voorraad.min' => 'Voorraad kan niet negatief zijn.',
        ];
    }
}
