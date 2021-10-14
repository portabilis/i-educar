<?php

namespace Database\Factories;

use App\Models\LegacyEducationNetwork;
use App\Models\LegacyInstitution;
use App\Models\LegacyOrganization;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
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
            'ref_usuario_cad' => LegacyUser::factory()->unique()->make(),
            'ref_cod_instituicao' => LegacyInstitution::factory()->unique()->make(),
            'ref_cod_escola_rede_ensino' => LegacyEducationNetwork::factory()->create(),
            'sigla' => $this->faker->asciify(),
            'data_cadastro' => now(),
            'ref_idpes' => LegacyOrganization::factory()->create(),
        ];
    }
}
