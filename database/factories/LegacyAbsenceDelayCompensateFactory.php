<?php

namespace Database\Factories;

use App\Models\LegacyAbsenceDelayCompensate;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyAbsenceDelayCompensateFactory extends Factory
{
    protected $model = LegacyAbsenceDelayCompensate::class;

    public function definition()
    {
        return [
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ref_ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'ref_cod_servidor' => EmployeeFactory::new()->create(),
            'data_inicio' => $this->faker->date(),
            'data_fim' => $this->faker->date(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
        ];
    }
}
