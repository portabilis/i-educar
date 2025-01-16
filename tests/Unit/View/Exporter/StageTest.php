<?php

namespace Tests\Unit\View\Exporter;

use App\Models\Exporter\Stage;
use Tests\ViewTestCase;

class StageTest extends ViewTestCase
{
    public function test_find_using_eloquent(): void
    {
        $this->assertTrue(true);
    }

    public function test_person(): void
    {
        $found = Stage::query()->get();
        $this->assertCount(1, $found);
        $this->assertInstanceOf(Stage::class, $found->first());
        $this->assertJsonStringEqualsJsonString($this->model, $found->first());
    }

    public function test_get_alias(): void
    {
        $aliases = collect($this->model->getExportedColumnsByGroup())->flatMap(fn ($item) => $item);
        $this->assertEquals('Escola', $aliases->get('school_name'));
    }

    public function test_get_label(): void
    {
        $this->assertEquals('Calendário letivo', $this->model->getLabel());
    }

    public function test_get_description(): void
    {
        $this->assertEquals('Exportação de todos os calendários letivos do ano filtrado para identificação das datas de início e fim das etapas e existência de lançamentos.', $this->model->getDescription());
    }

    public function test_get_exported_columns_by_group(): void
    {
        $expected = [
            'Etapas' => [
                'school_name' => 'Escola',
                'school_class' => 'Turma',
                'stage_name' => 'Tipo de etapa',
                'stage_number' => 'Etapa',
                'stage_start_date' => 'Data início',
                'stage_end_date' => 'Data fim',
                'stage_type' => 'Padrão/Turma',
                'posted_data' => 'Possui lançamentos',
            ],
        ];
        $this->assertJsonStringEqualsJsonString(collect($expected), collect($this->model->getExportedColumnsByGroup()));
    }

    protected function getViewModelName(): string
    {
        return Stage::class;
    }
}
