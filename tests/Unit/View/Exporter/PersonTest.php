<?php

namespace Tests\Unit\View\Exporter;

use App\Models\Exporter\Person;
use Tests\ViewTestCase;

class PersonTest extends ViewTestCase
{
    public function testPerson(): void
    {
        $found = Person::query()->get();
        $this->assertCount(1, $found);
        $this->assertInstanceOf(Person::class, $found->first());
        $this->assertJsonStringEqualsJsonString($this->model, $found->first());
    }

    public function testRelationshipMother(): void
    {
        $found = Person::query()->mother([
            'id',
            'name',
            'email',
            'social_name',
            'cpf',
            'date_of_birth',
            'sus',
            'nis',
            'occupation',
            'organization',
            'monthly_income',
            'gender',
            'rg',
            'rg_issue_date',
            'rg_state_abbreviation',
        ])->first();
        $expected = [
            'ID da mãe',
            'Nome da mãe',
            'E-mail da mãe',
            'Nome social e/ou afetivo da mãe',
            'CPF da mãe',
            'Data de nascimento da mãe',
            'Número SUS da mãe',
            'NIS (PIS/PASEP) da mãe',
            'Ocupação da mãe',
            'Empresa da mãe',
            'Renda Mensal da mãe',
            'Gênero da mãe',
            'RG da mãe',
            'RG (Data Emissão) da mãe',
            'RG (Estado) da mãe',
        ];
        $this->assertInstanceOf(Person::class, $found);
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testRelationshipFather(): void
    {
        $found = Person::query()->father([
            'id',
            'name',
            'email',
            'social_name',
            'cpf',
            'date_of_birth',
            'sus',
            'nis',
            'occupation',
            'organization',
            'monthly_income',
            'gender',
            'rg',
            'rg_issue_date',
            'rg_state_abbreviation',
        ])->first();
        $expected = [
            'ID do pai',
            'Nome do pai',
            'E-mail do pai',
            'Nome social e/ou afetivo do pai',
            'CPF do pai',
            'Data de nascimento do pai',
            'Número SUS do pai',
            'NIS (PIS/PASEP) do pai',
            'Ocupação do pai',
            'Empresa do pai',
            'Renda Mensal do pai',
            'Gênero do pai',
            'RG do pai',
            'RG (Data Emissão) do pai',
            'RG (Estado) do pai',
        ];
        $this->assertInstanceOf(Person::class, $found);
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testRelationshipGuardian(): void
    {
        $found = Person::query()->guardian([
            'id',
            'name',
            'email',
            'social_name',
            'cpf',
            'date_of_birth',
            'sus',
            'nis',
            'occupation',
            'organization',
            'monthly_income',
            'gender',
            'rg',
            'rg_issue_date',
            'rg_state_abbreviation',
        ])->first();
        $expected = [
            'ID do responsável',
            'Nome do responsável',
            'E-mail do responsável',
            'Nome social e/ou afetivo do responsável',
            'CPF do responsável',
            'Data de nascimento do responsável',
            'Número SUS do responsável',
            'NIS (PIS/PASEP) do responsável',
            'Ocupação do responsável',
            'Empresa do responsável',
            'Renda Mensal do responsável',
            'Gênero do responsável',
            'RG do responsável',
            'RG (Data Emissão) do responsável',
            'RG (Estado) do responsável',
        ];
        $this->assertInstanceOf(Person::class, $found);
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testGetAlias(): void
    {
        $aliases = collect($this->model->getExportedColumnsByGroup())->flatMap(fn ($item) => $item);
        $this->assertEquals('ID', $aliases->get('id'));
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('Pessoas', $this->model->getLabel());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('Exportação de pessoas', $this->model->getDescription());
    }

    public function testGetExportedColumnsByGroup(): void
    {
        $expected = [
            'Aluno' => [
                'id' => 'ID',
                'name' => 'Nome',
                'social_name' => 'Nome social e/ou afetivo',
                'cpf' => 'CPF',
                'rg' => 'RG',
                'rg_issue_date' => 'RG (Data Emissão)',
                'rg_state_abbreviation' => 'RG (Estado)',
                'date_of_birth' => 'Data de nascimento',
                'email' => 'E-mail',
                'sus' => 'Número SUS',
                'nis' => 'NIS (PIS/PASEP)',
                'occupation' => 'Ocupação',
                'organization' => 'Empresa',
                'monthly_income' => 'Renda Mensal',
                'gender' => 'Gênero',
                'race' => 'Raça',
                'phones.phones' => 'Telefones',
                'disabilities.disabilities' => 'Deficiências',
            ],
            'Endereço' => [
                'place.address' => 'Logradouro',
                'place.number' => 'Número',
                'place.complement' => 'Complemento',
                'place.neighborhood' => 'Bairro',
                'place.postal_code' => 'CEP',
                'place.latitude' => 'Latitude',
                'place.longitude' => 'Longitude',
                'place.city' => 'Cidade',
                'place.state_abbreviation' => 'Sigla do Estado',
                'place.state' => 'Estado',
                'place.country' => 'País',
            ],
            'Mãe' => [
                'mother.id' => 'ID da mãe',
                'mother.name' => 'Nome da mãe',
                'mother.social_name' => 'Nome social e/ou afetivo da mãe',
                'mother.cpf' => 'CPF da mãe',
                'mother.rg' => 'RG da mãe',
                'mother.rg_issue_date' => 'RG (Data Emissão) da mãe',
                'mother.rg_state_abbreviation' => 'RG (Estado) da mãe',
                'mother.date_of_birth' => 'Data de nascimento da mãe',
                'mother.email' => 'E-mail da mãe',
                'mother.sus' => 'Número SUS da mãe',
                'mother.nis' => 'NIS (PIS/PASEP) da mãe',
                'mother.occupation' => 'Ocupação da mãe',
                'mother.organization' => 'Empresa da mãe',
                'mother.monthly_income' => 'Renda Mensal da mãe',
                'mother.gender' => 'Gênero da mãe',
            ],
            'Pai' => [
                'father.id' => 'ID do pai',
                'father.name' => 'Nome do pai',
                'father.social_name' => 'Nome social e/ou afetivo do pai',
                'father.cpf' => 'CPF do pai',
                'father.rg' => 'RG do pai',
                'father.rg_issue_date' => 'RG (Data Emissão) do pai',
                'father.rg_state_abbreviation' => 'RG (Estado) do pai',
                'father.date_of_birth' => 'Data de nascimento do pai',
                'father.email' => 'E-mail do pai',
                'father.sus' => 'Número SUS do pai',
                'father.nis' => 'NIS (PIS/PASEP) do pai',
                'father.occupation' => 'Ocupação do pai',
                'father.organization' => 'Empresa do pai',
                'father.monthly_income' => 'Renda Mensal do pai',
                'father.gender' => 'Gênero do pai',
            ],
            'Responsável' => [
                'guardian.id' => 'ID do responsável',
                'guardian.name' => 'Nome do responsável',
                'guardian.social_name' => 'Nome social e/ou afetivo do responsável',
                'guardian.cpf' => 'CPF do responsável',
                'guardian.rg' => 'RG do responsável',
                'guardian.rg_issue_date' => 'RG (Data Emissão) do responsável',
                'guardian.rg_state_abbreviation' => 'RG (Estado) do responsável',
                'guardian.date_of_birth' => 'Data de nascimento do responsável',
                'guardian.email' => 'E-mail do responsável',
                'guardian.sus' => 'Número SUS do responsável',
                'guardian.nis' => 'NIS (PIS/PASEP) do responsável',
                'guardian.occupation' => 'Ocupação do responsável',
                'guardian.organization' => 'Empresa do responsável',
                'guardian.monthly_income' => 'Renda Mensal do responsável',
                'guardian.gender' => 'Gênero do responsável',
            ],
        ];
        $this->assertJsonStringEqualsJsonString(collect($expected), collect($this->model->getExportedColumnsByGroup()));
    }

    public function testRelationshipDisabilities(): void
    {
        $found = Person::query()->disabilities()->first();
        $this->assertInstanceOf(Person::class, $found);
        $expected = [
            'Deficiências',
        ];
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testRelationshipPhones(): void
    {
        $found = Person::query()->phones()->first();
        $this->assertInstanceOf(Person::class, $found);
        $expected = [
            'Telefones',
        ];
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testRelationshipPlace(): void
    {
        $found = Person::query()->place([
            'address',
            'number',
            'complement',
            'neighborhood',
            'postal_code',
            'latitude',
            'longitude',
            'city',
            'state_abbreviation',
            'state',
            'country',
        ])->first();
        $this->assertInstanceOf(Person::class, $found);
        $expected = [
            'Logradouro',
            'Número',
            'Complemento',
            'Bairro',
            'CEP',
            'Latitude',
            'Longitude',
            'Cidade',
            'Sigla do Estado',
            'Estado',
            'País',
        ];
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    protected function getViewModelName(): string
    {
        return Person::class;
    }
}
