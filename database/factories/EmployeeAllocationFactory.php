<?php

namespace Database\Factories;

use App\Models\Employee;
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
            'carga_horaria',
            'periodo',
            'hora_final',
            'hora_inicial',
            'dia_semana',
            'ano',
            'data_admissao',
            'ref_ref_cod_instituicao',
            'ref_cod_escola' => LegacySchoolFactory::new()->create(),
            'ref_cod_servidor' => Employee::new()->create(),
            'ref_cod_servidor_funcao',
            'ref_cod_funcionario_vinculo',
            'hora_atividade',
            'horas_excedentes',
            'data_saida'
        ];
    }
}
