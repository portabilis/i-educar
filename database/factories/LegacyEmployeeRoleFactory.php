<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeAllocation;
use App\Models\LegacyEmployeeRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEmployeeRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEmployeeRole::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'cod_servidor_funcao',
            'matricula',
            'ref_cod_funcao',
            'ref_cod_servidor',
            'ref_ref_cod_instituicao',
        ];
    }
}
