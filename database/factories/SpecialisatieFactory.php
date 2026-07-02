<?php

namespace Database\Factories;

use App\Models\Medewerker\SpecialisatieModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpecialisatieModel>
 */
class SpecialisatieFactory extends Factory
{
    protected $model = SpecialisatieModel::class;

    public function definition(): array
    {
        return [
            'naam' => fake()->unique()->lexify('Spec-????'),
            'beschrijving' => fake()->optional()->sentence(),
        ];
    }
}
