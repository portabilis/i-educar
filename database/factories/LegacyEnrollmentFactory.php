<?php

namespace Database\Factories;

use App\Models\LegacyEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEnrollmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEnrollment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(),
            'ref_cod_turma' => LegacySchoolClassFactory::new()->create(),
            'sequencial' => 1,
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'data_cadastro' => now(),
            'data_enturmacao' => now(),
        ];
    }

    public function active(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'ativo' => 1
            ]);
        });
    }

    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'ativo' => 0
            ]);
        });
    }
}
