<?php

namespace Database\Factories;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionType;
use App\Models\LegacyLevel;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineExemptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineExemption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_matricula' => LegacyRegistration::factory()->create(),
            'ref_cod_disciplina' => LegacyDiscipline::factory()->create(),
            'ref_cod_escola' => LegacySchool::factory()->create(),
            'ref_cod_serie' => LegacyLevel::factory()->create(),
            'ref_cod_tipo_dispensa' => LegacyExemptionType::factory()->create(),
            'ref_usuario_cad' => LegacyUser::factory()->unique()->make(),
            'data_cadastro' => now(),
            'ativo' => 1,
        ];
    }
}
