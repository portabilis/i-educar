<?php

namespace Database\Factories;

use App\Models\LegacyPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyPersonFactory extends Factory
{
    protected $model = LegacyPerson::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'data_cad' => now(),
            'tipo' => 'F',
            'situacao' => 'A',
            'origem_gravacao' => 'M',
            'operacao' => 'I',
            'email' => $this->faker->email(),
        ];
    }

    public function current(): LegacyPerson
    {
        return LegacyPerson::query()->first() ?? $this->create();
    }
}
