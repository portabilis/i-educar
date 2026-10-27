<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\LegacySchool;
use App\Models\State;
use App\Models\View\SchoolData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * A view relatorio.view_dados_escola não aceita gravação: create() monta os
 * cadastros de onde ela lê (pessoa jurídica, escola, telefones, endereço e INEP)
 * e devolve o registro lido da própria view, já com a formatação dela (nome em
 * maiúsculas, telefones com máscara). make() só preenche os atributos, sem banco.
 *
 * Os atributos usam os nomes das colunas da view:
 *
 *     SchoolDataFactory::new()->create(['municipio' => 'Içara', 'uf_municipio' => 'SC']);
 *     SchoolDataFactory::new()->withoutAddress()->create();
 */
class SchoolDataFactory extends Factory
{
    protected $model = SchoolData::class;

    public function definition(): array
    {
        return [
            'nome' => 'Escola Municipal ' . $this->faker->lastName(),
            'email' => $this->faker->safeEmail(),
            'logradouro' => 'Rua ' . $this->faker->lastName(),
            'numero' => (string) $this->faker->numberBetween(1, 9999),
            'bairro' => 'Bairro ' . $this->faker->lastName(),
            'cep' => $this->faker->numerify('########'),
            'municipio' => $this->faker->city(),
            'uf_municipio' => $this->faker->stateAbbr(),
            'inep' => (string) $this->faker->numberBetween(10000000, 99999999),
            'telefone_ddd' => $this->faker->numerify('##'),
            'telefone' => $this->faker->numerify('3###-####'),
            'celular_ddd' => $this->faker->numerify('##'),
            'celular' => $this->faker->numerify('9####-####'),
        ];
    }

    /**
     * Escola sem endereço: logradouro, número, bairro, município, UF e CEP nulos.
     */
    public function withoutAddress(): static
    {
        return $this->state([
            'logradouro' => null,
            'numero' => null,
            'bairro' => null,
            'cep' => null,
            'municipio' => null,
            'uf_municipio' => null,
        ]);
    }

    /**
     * Endereço sem número, que a view devolve como "S/N".
     */
    public function withoutNumber(): static
    {
        return $this->state([
            'numero' => null,
        ]);
    }

    public function withoutPhone(): static
    {
        return $this->state([
            'telefone_ddd' => null,
            'telefone' => null,
        ]);
    }

    public function withoutCellphone(): static
    {
        return $this->state([
            'celular_ddd' => null,
            'celular' => null,
        ]);
    }

    public function withoutInep(): static
    {
        return $this->state([
            'inep' => null,
        ]);
    }

    protected function store(Collection $results)
    {
        $results->each(function (SchoolData $schoolData) {
            $school = $this->createSchool($schoolData);

            $schoolData->setRawAttributes(
                SchoolData::query()->findOrFail($school->getKey())->getAttributes(),
                true
            );

            $schoolData->exists = true;
        });
    }

    private function createSchool(SchoolData $schoolData): LegacySchool
    {
        $person = LegacyPersonFactory::new()->create([
            'nome' => $schoolData->nome,
            'tipo' => 'J',
            'email' => $schoolData->email,
        ]);

        $school = LegacySchoolFactory::new()->create([
            'ref_idpes' => LegacyOrganizationFactory::new()->create([
                'idpes' => $person,
                'fantasia' => $schoolData->nome,
            ]),
        ]);

        // Na view, o tipo 1 é o telefone e o tipo 3 é o celular
        $phones = [
            1 => [$schoolData->telefone_ddd, $schoolData->telefone],
            3 => [$schoolData->celular_ddd, $schoolData->celular],
        ];

        foreach ($phones as $type => [$ddd, $phone]) {
            if ($phone) {
                LegacyPhoneFactory::new()->create([
                    'idpes' => $person,
                    'tipo' => $type,
                    'ddd' => $ddd,
                    'fone' => preg_replace('/\D/', '', $phone),
                ]);
            }
        }

        if ($schoolData->logradouro || $schoolData->municipio) {
            $place = PlaceFactory::new()->create([
                'city_id' => $this->city($schoolData),
                'address' => $schoolData->logradouro,
                'number' => $schoolData->numero,
                'complement' => null,
                'neighborhood' => $schoolData->bairro,
                'postal_code' => $schoolData->cep ? preg_replace('/\D/', '', $schoolData->cep) : null,
            ]);

            $school->addresses()->attach($place, ['type' => 1]);
        }

        if ($schoolData->inep) {
            SchoolInepFactory::new()->create([
                'cod_escola' => $school,
                'cod_escola_inep' => $schoolData->inep,
            ]);
        }

        return $school;
    }

    /**
     * Reaproveita o município e a UF quando já existem, como os seeds de cidades.
     */
    private function city(SchoolData $schoolData): City
    {
        $state = State::query()->where('abbreviation', $schoolData->uf_municipio)->first()
            ?? StateFactory::new()->create(['abbreviation' => $schoolData->uf_municipio]);

        return City::query()->where('state_id', $state->getKey())->where('name', $schoolData->municipio)->first()
            ?? CityFactory::new()->create(['state_id' => $state, 'name' => $schoolData->municipio]);
    }
}
