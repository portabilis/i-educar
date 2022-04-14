<?php

namespace Database\Factories;

use App\Models\LegacyRoundingTable;
use Illuminate\Database\Eloquent\Factories\Factory;
use RegraAvaliacao_Model_Nota_TipoValor;

class LegacyRoundingTableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyRoundingTable::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'instituicao_id' => LegacyInstitutionFactory::new()->unique()->make(),
            'nome' => $this->faker->words(3, true),
            'tipo_nota' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function numeric(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
            ]);
        });
    }

    public function conceitual(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL,
            ]);
        });
    }
}
