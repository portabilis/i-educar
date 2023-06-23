<?php

namespace Tests\Unit\View\Exporter;

use App\Models\Exporter\Teacher;
use Tests\ViewTestCase;

class TeacherTest extends ViewTestCase
{
    public function testTeacher(): void
    {
        $found = Teacher::query()->get();
        $this->assertCount(1, $found);
        $this->assertInstanceOf(Teacher::class, $found->first());
        $this->assertJsonStringEqualsJsonString($this->model, $found->first());
    }

    public function testGetAlias(): void
    {
        $aliases = collect($this->model->getExportedColumnsByGroup())->flatMap(fn ($item) => $item);
        $this->assertEquals('ID Pessoa', $aliases->get('id'));
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('Professores', $this->model->getLabel());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('Os dados exportados serão contabilizados por quantidade de professores(as) alocados(as) no ano filtrado, agrupando as informações de cursos de formação dos docentes.', $this->model->getDescription());
    }

    public function testGetExportedColumnsByGroup(): void
    {
        $expected = [
            'Códigos' => [
                'id' => 'ID Pessoa',
                'school_id' => 'ID Escola',
                'school_class_id' => 'ID Turma',
                'grade_id' => 'ID Série',
                'course_id' => 'ID Curso',
            ],
            'Professor' => [
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
            ],
            'Escola' => [
                'school' => 'Escola',
                'school_class' => 'Turma',
                'grade' => 'Série',
                'course' => 'Curso',
                'year' => 'Ano',
                'disciplines.disciplines' => 'Disciplinas',
                'enrollments' => 'Matrículas',
            ],
            'Informações' => [
                'phones.phones' => 'Telefones',
                'disabilities.disabilities' => 'Deficiências',
                'schooling_degree' => 'Escolaridade',
                'high_school_type' => 'Tipo de ensino médio cursado',
                'employee_postgraduates_complete' => 'Pós-Graduações concluídas',
                'continuing_education_course' => 'Outros cursos de formação continuada',
                'employee_graduation_complete' => 'Curso(s) superior(es) concluído(s)',
                'allocations.funcao_exercida' => 'Função exercida',
                'allocations.tipo_vinculo' => 'Tipo de vínculo',
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
        ];
        $this->assertJsonStringEqualsJsonString(collect($expected), collect($this->model->getExportedColumnsByGroup()));
    }

    public function testRelationshipPerson(): void
    {
        $found = Teacher::query()->person([
            'id',
            'name',
            'email',
        ])->first();
        $expected = [
            'person.id',
            'person.name',
            'person.email',
        ];
        $this->assertInstanceOf(Teacher::class, $found);
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testRelationshipDisabilities(): void
    {
        $found = Teacher::query()->disabilities()->first();
        $this->assertInstanceOf(Teacher::class, $found);
        $expected = [
            'Deficiências',
        ];
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testRelationshipPhones(): void
    {
        $found = Teacher::query()->phones()->first();
        $this->assertInstanceOf(Teacher::class, $found);
        $expected = [
            'Telefones',
        ];
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    public function testRelationshipPlace(): void
    {
        $found = Teacher::query()->place([
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
        $this->assertInstanceOf(Teacher::class, $found);
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

    public function testRelationshipDisciplies(): void
    {
        $found = Teacher::query()->disciplines()->first();
        $this->assertInstanceOf(Teacher::class, $found);
        $expected = [
            'Disciplinas',
        ];
        $this->assertEquals($expected, array_keys($found->getAttributes()));
    }

    protected function getViewModelName(): string
    {
        return Teacher::class;
    }
}
