<?php

namespace Database\Factories;

use App\Models\EmployeeAllocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeAllocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeAllocation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'carga_horaria' => $this->faker->randomNumber(3),
            'periodo' => fn () => LegacyPeriodFactory::new()->create(),
            'hora_inicial' => '07:45',
            'hora_final' => '11:45',
            'dia_semana' => $this->faker->numberBetween(0, 7),
            'ano' => now()->year,
            'data_admissao' => $this->faker->date(),
            'ref_ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ref_cod_servidor' => EmployeeFactory::new()->create(),
            'ref_cod_servidor_funcao' => fn () => LegacyEmployeeRoleFactory::new()->create(),
            'ref_cod_funcionario_vinculo' => $this->faker->randomDigitNotZero(),
            'hora_atividade' => $this->faker->time(),
            'horas_excedentes' => $this->faker->time(),
            'data_saida' => now(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
        ];
    }
}
