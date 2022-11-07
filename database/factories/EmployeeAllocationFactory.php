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
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'carga_horaria' => $this->faker->randomNumber(3),
            'periodo' => $this->faker->numberBetween(1, 3),
            'hora_inicial' => '07:45',
            'hora_final' => '11:45',
            'dia_semana' => $this->faker->numberBetween(0, 7),
            'ano' => now()->year,
            'data_admissao' => $this->faker->date(),
            'ref_ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'ref_cod_escola' => LegacySchoolFactory::new()->create(),
            'ref_cod_servidor' => EmployeeFactory::new()->create(),
            'ref_cod_servidor_funcao' => LegacyEmployeeRoleFactory::new()->create(),
            'ref_cod_funcionario_vinculo' => $this->faker->randomDigitNotZero(),
            'hora_atividade' => $this->faker->time(),
            'horas_excedentes' => $this->faker->time(),
            'data_saida' => now(),
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'ref_usuario_exc' => LegacyUserFactory::new()->create(),
        ];
    }
}
