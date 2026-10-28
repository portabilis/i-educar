<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\LegacyIndividual;
use App_Model_ZonaLocalizacao;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyIndividualFactory extends Factory
{
    protected $model = LegacyIndividual::class;

    public function definition(): array
    {
        return [
            // Pelo mesmo motivo da jurídica: quem está em cadastro.fisica é
            // sempre pessoa física, e as buscas das telas filtram por tipo = 'F'.
            'idpes' => fn () => LegacyPersonFactory::new()->create([
                'nome' => $this->faker->firstNameFemale(),
                'tipo' => 'F',
            ]),
            'operacao' => 'I',
            'origem_gravacao' => 'M',
            'zona_localizacao_censo' => App_Model_ZonaLocalizacao::URBANA,
            // Adulta, para não cair em nenhuma regra de idade sem o teste pedir
            'data_nasc' => now()->subYears(30),
            'sexo' => 'F',
            'ideciv' => fn () => LegacyMaritalStatusFactory::new()->current(),
            'pais_residencia' => PaisResidencia::BRASIL,
            'idmun_nascimento' => City::query()->inRandomOrder()->first(),
            'idpes_pai' => null,
            'idpes_mae' => null,
        ];
    }

    public function current(): LegacyIndividual
    {
        return LegacyIndividual::query()->first() ?? $this->create([
            'idpes' => fn () => LegacyPersonFactory::new()->current(),
        ]);
    }

    public function withName(string $name): static
    {
        return $this->afterCreating(function (LegacyIndividual $individual) use ($name) {
            $individual->person->nome = $name;
            $individual->person->save();
        });
    }

    public function withDocument(?string $rg = null, ?string $birthCertificate = null): static
    {
        return $this->afterCreating(function (LegacyIndividual $individual) use ($rg, $birthCertificate) {
            LegacyDocumentFactory::new()->create([
                'idpes' => $individual->getKey(),
                'rg' => $rg,
                'certidao_nascimento' => $birthCertificate,
            ]);
        });
    }

    public function withAge(int $age): static
    {
        $year = now()->year - $age;
        $date = $this->faker->dateTimeBetween("$year-01-01", "$year-12-31");

        return $this->state([
            'data_nasc' => $date,
        ]);
    }

    public function father(): self
    {
        return $this->state(function () {
            return [
                'idpes_pai' => fn () => LegacyIndividualFactory::new()->create(),
            ];
        });
    }

    public function mother(): self
    {
        return $this->state(function () {
            return [
                'idpes_mae' => fn () => LegacyIndividualFactory::new()->create(),
            ];
        });
    }

    public function guardian(): self
    {
        return $this->state(function () {
            return [
                'idpes_responsavel' => fn () => LegacyIndividualFactory::new()->create(),
            ];
        });
    }
}
