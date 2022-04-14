<?php

namespace Database\Factories;

use App\Models\LegacyInstitution;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyInstitutionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyInstitution::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => 1,
            'ref_idtlog' => 'AV',
            'ref_sigla_uf' => 'SC',
            'cep' => $this->faker->numerify('########'),
            'cidade' => $this->faker->city,
            'bairro' => $this->faker->lastName,
            'logradouro' => $this->faker->address,
            'nm_responsavel' => $this->faker->name,
            'data_cadastro' => now(),
            'nm_instituicao' => $this->faker->company,
        ];
    }

    public function unique(): self
    {
        return $this->state(function () {
            $institution = LegacyInstitution::query()->first();

            if (empty($institution)) {
                $institution = LegacyInstitutionFactory::new()->create();
            }

            return [
                'cod_instituicao' => $institution->getKey()
            ];
        });
    }
}
