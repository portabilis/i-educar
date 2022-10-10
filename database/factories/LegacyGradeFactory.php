<?php

namespace Database\Factories;

use App\Models\LegacyGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGradeFactory extends Factory
{
    protected $model = LegacyGrade::class;

    public function definition(): array
    {
        return [
            'nm_serie' => $this->faker->words(3, true),
            'ref_usuario_cad' => 1,
            'ref_cod_curso' => fn () => LegacyCourseFactory::new()->create(),
            'etapa_curso' => $this->faker->randomElement([1, 2, 3, 4]),
            'carga_horaria' => 800,
            'data_cadastro' => $this->faker->dateTime(),
            'idade_inicial' => $initial = $this->faker->numberBetween(0, 20),
            'idade_final' => $initial + 1,
            'descricao' => $this->faker->word(),
            'concluinte' => $this->faker->randomNumber(1),
            'ativo' => 1,
            'importar_serie_pre_matricula' => false,
            'dias_letivos' => $this->faker->numberBetween(100, 200),
        ];
    }
}
