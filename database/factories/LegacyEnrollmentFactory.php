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
     */
    public function definition(): array
    {
        return [
            'ref_cod_matricula' => fn () => LegacyRegistrationFactory::new()->create(),
            'ref_cod_turma' => fn () => LegacySchoolClassFactory::new()->create(),
            'sequencial' => 1,
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'turno_id' => fn () => LegacyPeriodFactory::new()->morning(),
            'data_enturmacao' => now(),
        ];
    }

    public function active(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'ativo' => 1,
            ]);
        });
    }

    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'ativo' => 0,
            ]);
        });
    }
}
