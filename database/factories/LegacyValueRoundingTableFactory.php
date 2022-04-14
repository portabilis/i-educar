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
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'tabela_arredondamento_id' => LegacyRoundingTableFactory::new()->make(),
            'nome' => $this->faker->randomNumber(1),
        ];
    }
}
