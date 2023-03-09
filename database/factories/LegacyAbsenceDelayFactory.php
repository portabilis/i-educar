<?php

namespace Database\Factories;

use App\Models\LegacyAbsenceDelay;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyAbsenceDelayFactory extends Factory
{
    protected $model = LegacyAbsenceDelay::class;

    public function definition()
    {
        return [
            'ref_cod_escola' => LegacySchoolFactory::new()->create(),
            'ref_ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'ref_cod_servidor' => EmployeeFactory::new()->create(),
            'tipo' => $this->faker->randomElement([1, 2]),
            'data_falta_atraso' => $this->faker->date(),
            'qtd_horas' => $this->faker->randomNumber(),
            'qtd_min' => $this->faker->randomNumber(),
            'justificada' => $this->faker->randomElement([1, 2]),
            'ref_cod_servidor_funcao' => LegacyEmployeeRoleFactory::new()->create(),
            'ref_usuario_cad' => LegacyUserFactory::new()->create(),
            'ref_usuario_exc' => LegacyUserFactory::new()->create(),
        ];
    }
}
