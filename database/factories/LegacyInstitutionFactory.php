<?php

namespace Database\Factories;

use App\Models\LegacyInstitution;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyInstitutionFactory extends Factory
{
    protected $model = LegacyInstitution::class;

    public function definition(): array
    {
        $date = now()->month(3)->day(31);

        return [
            'ref_usuario_cad' => 1,
            'ref_idtlog' => 'AV',
            'ref_sigla_uf' => 'SC',
            'cep' => $this->faker->numerify('########'),
            'cidade' => $this->faker->city(),
            'bairro' => $this->faker->lastName(),
            'logradouro' => $this->faker->address(),
            'nm_responsavel' => $this->faker->name(),
            'data_cadastro' => now(),
            'nm_instituicao' => $this->faker->company(),
            'data_base_remanejamento' => $date,
            'data_base_transferencia' => $date,
            'data_expiracao_reserva_vaga' => $date,
            'data_base_matricula' => $date->year(1990),
            'data_fechamento' => $date->year(1990),
            'data_educacenso' => now()->month(5)->day(31),
        ];
    }

    public function current(): LegacyInstitution
    {
        return LegacyInstitution::query()->first() ?? $this->create();
    }

    public function unique(): self
    {
        return $this->state(function () {
            $institution = LegacyInstitution::query()->first();

            if (empty($institution)) {
                $institution = LegacyInstitutionFactory::new()->create();
            }

            return [
                'cod_instituicao' => $institution->getKey(),
            ];
        });
    }
}
