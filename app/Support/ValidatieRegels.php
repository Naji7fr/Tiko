<?php

namespace App\Support;

/**
 * Gedeelde validatieregels voor naam- en e-mailvelden.
 */
final class ValidatieRegels
{
    public const NAAM_REGEX = '/^[\pL\s\-\'\.]+$/u';

    /** @return list<string> */
    public static function voornaam(int $max = 50): array
    {
        return ['required', 'string', "max:{$max}", 'regex:'.self::NAAM_REGEX];
    }

    /** @return list<string> */
    public static function achternaam(int $max = 50): array
    {
        return ['required', 'string', "max:{$max}", 'regex:'.self::NAAM_REGEX];
    }

    /** @return list<string> */
    public static function tussenvoegsel(int $max = 20): array
    {
        return ['nullable', 'string', "max:{$max}", 'regex:'.self::NAAM_REGEX];
    }

    /** @return array<string, string> */
    public static function naamBerichten(): array
    {
        return [
            'voornaam.regex' => 'Voornaam mag geen cijfers of speciale tekens bevatten.',
            'achternaam.regex' => 'Achternaam mag geen cijfers of speciale tekens bevatten.',
            'tussenvoegsel.regex' => 'Tussenvoegsel mag geen cijfers of speciale tekens bevatten.',
        ];
    }

    /** @return array<string, string> */
    public static function emailBerichten(): array
    {
        return [
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
        ];
    }
}
