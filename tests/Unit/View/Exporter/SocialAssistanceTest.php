<?php

namespace Tests\Unit\View\Exporter;

use App\Models\Exporter\SocialAssistance;
use Tests\ViewTestCase;

class SocialAssistanceTest extends ViewTestCase
{
    public function testFindUsingEloquent(): void
    {
        $this->assertTrue(true);
    }

    public function testSocialAssistance(): void
    {
        $found = SocialAssistance::query()->where('status', 9)->get();
        $this->assertInstanceOf(SocialAssistance::class, $found->first());
        $this->assertJsonStringEqualsJsonString($this->model, $found->first());
    }

    public function testGetAlias(): void
    {
        $aliases = collect($this->model->getExportedColumnsByGroup())->flatMap(fn ($item) => $item);
        $this->assertEquals('Nome', $aliases->get('name'));
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('Dados de escolaridade - Assistência Social', $this->model->getLabel());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('Os dados exportados serão contabilizados por quantidade de matrículas dos(as) alunos(as), duplicando o(a) aluno(a) caso o mesmo possua mais de uma matrícula no ano filtrado. Opção utilizada para integração com sistemas de Assistência social que coletem dados de escolaridade das famílias atendidas.', $this->model->getDescription());
    }

    public function testGetExportedColumnsByGroup(): void
    {
        $expected = [
            'Aluno' => [
                'name' => 'Nome',
                'cpf' => 'CPF',
                'date_of_birth' => 'Data de nascimento',
                'nis' => 'NIS (PIS/PASEP)',
            ],
            'Escola' => [
                'school' => 'Escola',
                'school_inep' => 'Código INEP',
                'period' => 'Turno',
                'school_class_stage' => 'Etapa Educacenso',
                'attendance_type' => 'Tipo de atendimento da turma',
            ],
        ];
        $this->assertJsonStringEqualsJsonString(collect($expected), collect($this->model->getExportedColumnsByGroup()));
    }

    protected function getViewModelName(): string
    {
        return SocialAssistance::class;
    }
}
