<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGradeFactory extends Factory
{
    protected $model = LegacyGrade::class;

    public function definition(): array
    {
        return [
            'ref_usuario_cad' => 1,
            'nm_serie' => $this->faker->word(),
            'descricao' => $this->faker->word(),
            'etapa_curso' => $this->faker->randomNumber(1),
            'concluinte' => $this->faker->randomNumber(1),
            'carga_horaria' => $this->faker->randomFloat(),
            'data_cadastro' =>  now(),
            'ativo' => 1,
            'importar_serie_pre_matricula' => $this->faker->boolean(),
            'ref_cod_curso' => LegacyCourse::factory(),
        ];
    }
}
