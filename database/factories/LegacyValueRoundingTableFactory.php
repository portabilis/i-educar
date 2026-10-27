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
            'nome' => '0',
            'descricao' => null,
            'valor_minimo' => 0,
            'valor_maximo' => 0.4,
            'casa_decimal_exata' => null,
            'acao' => null,
            'observacao' => null,
        ];
    }

    /**
     * As 21 faixas da tabela numérica padrão, de 0 a 10 em passos de 0,5,
     * como a instalação as traz.
     */
    public function numericBands(): self
    {
        $bands = [];

        for ($value = 0; $value <= 10; $value += 0.5) {
            $bands[] = [
                'nome' => (string) $value,
                'valor_minimo' => $value,
                'valor_maximo' => min($value + 0.4, 10),
            ];
        }

        return $this->count(count($bands))->sequence(...$bands);
    }
}
