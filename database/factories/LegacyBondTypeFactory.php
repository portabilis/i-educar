<?php

namespace Database\Factories;

use App\Models\LegacyBondType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyBondTypeFactory extends Factory
{
    protected $model = LegacyBondType::class;

    public function definition(): array
    {
        return [
            'nm_vinculo' => $this->faker->randomElement(['Efetivo', 'Contratado', 'Comissionado', 'Estagiário']),
            'abreviatura' => fn (array $attributes) => mb_strtoupper(mb_substr($attributes['nm_vinculo'], 0, 4)),
        ];
    }

    public function current(): LegacyBondType
    {
        return LegacyBondType::query()->first() ?? $this->create();
    }
}
