<?php

namespace Database\Factories;

use App\Models\LegacySchoolHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->current(),
            'ref_cod_aluno' => LegacyStudentFactory::new()->create(),
            'sequencial' => 1,
            'ref_usuario_exc' => LegacyUserFactory::new()->current(),
            'ref_usuario_cad' => LegacyUserFactory::new()->current(),
            'ano' => now()->year,
            'carga_horaria' => $this->faker->randomNumber(),
            'dias_letivos' => $this->faker->randomNumber(),
            'escola' => $this->faker->word(),
            'escola_cidade' => CityFactory::new()->make()->name,
            'escola_uf' => StateFactory::new()->make()->abbreviation,
            'observacao' => $this->faker->text(),
            'aprovado' => 1,
            'ativo' => 1,
            'faltas_globalizadas' => $this->faker->randomNumber(),
            'nm_serie' => $this->faker->word(),
            'origem' => 1,
            'extra_curricular' => 0,
            'ref_cod_matricula' => fn () => LegacyRegistrationFactory::new()->create(),
            'import' => 1,
            'frequencia' => $this->faker->randomNumber(3),
            'registro' => $this->faker->word(),
            'livro' => $this->faker->word(),
            'folha' => $this->faker->word(),
            'historico_grade_curso_id' => null,
            'nm_curso' => $this->faker->word(),
            'aceleracao' => $this->faker->randomNumber(),
            'ref_cod_escola' => LegacySchoolFactory::new()->create(),
            'dependencia' => $this->faker->boolean,
            'posicao' => $this->faker->randomNumber(),
        ];
    }
}
