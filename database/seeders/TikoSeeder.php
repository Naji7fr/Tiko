<?php

namespace Database\Seeders;

use App\Models\Medewerker\SpecialisatieModel;
use Illuminate\Database\Seeder;

/**
 * Seeder voor Tiko-stamgegevens (specialisaties).
 */
class TikoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Fade', 'Baard', 'Kleuren'] as $naam) {
            SpecialisatieModel::firstOrCreate(['naam' => $naam]);
        }
    }
}
