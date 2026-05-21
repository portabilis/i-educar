<?php

namespace Database\Factories;

use App\Models\LegacyGradeSequence;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGradeSequenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyGradeSequence::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ref_serie_origem' => static fn () => LegacyGradeFactory::new()->create(),
            'ref_serie_destino' => static fn () => LegacyGradeFactory::new()->create(),
            'ref_usuario_cad' => static fn () => LegacyUserFactory::new(),
        ];
    }
}
