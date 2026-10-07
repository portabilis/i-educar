<?php

namespace Database\Factories;

use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyOrganizationFactory extends Factory
{
    protected $model = LegacyOrganization::class;

    public function definition(): array
    {
        return [
            // Quem está em cadastro.juridica é sempre pessoa jurídica, e as
            // buscas das telas filtram por tipo = 'J'. O factory da pessoa sorteia
            // o tipo entre F e J, então aqui ele é fixado.
            'idpes' => fn () => LegacyPersonFactory::new()->create([
                'nome' => $this->faker->company(),
                'tipo' => 'J',
            ]),
            'cnpj' => $this->faker->numerify('##############'),
            'insc_estadual' => $this->faker->numerify('########'),
            'origem_gravacao' => $this->faker->randomElement(['M', 'U', 'C', 'O']),
            'idpes_cad' => fn () => LegacyUserFactory::new()->current(),
            'operacao' => $this->faker->randomElement(['I', 'A', 'E']),
            'fantasia' => fn (array $attributes) => LegacyPerson::query()->find($attributes['idpes'])->nome,
        ];
    }
}
