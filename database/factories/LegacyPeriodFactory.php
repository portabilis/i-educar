<?php

namespace Database\Factories;

use App\Models\LegacyPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyPeriodFactory extends Factory
{
    protected $model = LegacyPeriod::class;

    public function definition(): array
    {
        return [
            'nome' => 'Turno ' . substr($this->faker->colorName(), 0, 9),
            'ativo' => 1,
        ];
    }

    public function morning(): LegacyPeriod
    {
        return LegacyPeriod::query()->where('nome', 'Matutino')->first() ?? $this->create([
            'nome' => 'Matutino',
        ]);
    }

    public function afternoon(): LegacyPeriod
    {
        return LegacyPeriod::query()->where('nome', 'Vespertino')->first() ?? $this->create([
            'nome' => 'Vespertino',
        ]);
    }
}
