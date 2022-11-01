<?php

namespace Database\Factories;

use App\Models\LegacyStudentTransport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class LegacyStudentTransportFactory extends Factory
{
    protected $model = LegacyStudentTransport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'aluno_id' => LegacyStudentFactory::new(),
            'responsavel' => $this->faker->numerify(),
            'user_id' => LegacyUserFactory::new()
        ];
    }
}
