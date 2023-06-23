<?php

namespace Database\Factories;

use App\Models\LegacyValueRoundingTable;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyValueRoundingTableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyValueRoundingTable::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tabela_arredondamento_id' => fn () => LegacyRoundingTableFactory::new()->create(),
            'nome' => $this->faker->text(5),
            'descricao' => $this->faker->text(50),
            'valor_minimo' => $this->faker->randomNumber(1),
            'valor_maximo' => $this->faker->randomNumber(1),
            'casa_decimal_exata' => $this->faker->randomNumber(1),
            'acao' => $this->faker->randomNumber(1),
            'observacao' => $this->faker->text(50),
        ];
    }
}
