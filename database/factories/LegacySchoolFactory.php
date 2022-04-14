<?php

namespace Database\Factories;

use App\Models\LegacySchool;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchool::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'ref_cod_escola_rede_ensino' => LegacyEducationNetworkFactory::new()->create(),
            'sigla' => $this->faker->asciify(),
            'data_cadastro' => now(),
            'ref_idpes' => LegacyOrganizationFactory::new()->create(),
        ];
    }
}
