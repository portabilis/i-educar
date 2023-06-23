<?php

namespace Database\Factories;

use App\Models\LegacyEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEmployee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ref_cod_pessoa_fj' => fn () => LegacyIndividualFactory::new()->create()->idpes,
            'matricula' => $this->faker->randomDigitNotNull(),
            'senha' => $this->faker->password(),
            'email' => $this->faker->email(),
            'ativo' => 1,
        ];
    }

    public function current(): LegacyEmployee
    {
        return LegacyEmployee::query()->first() ?? $this->create([
            'ref_cod_pessoa_fj' => fn () => LegacyIndividualFactory::new()->current()->idpes,
        ]);
    }
}
