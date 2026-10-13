<?php

use iEducar\Reports\JsonDataSource;
use Tests\TestCase;

uses(TestCase::class);

function relatorioEmJson(array $args, ?string $main = null): object
{
    return new class($args, $main)
    {
        use JsonDataSource {
            getSqlMainReport as protected mainOriginal;
        }

        public function __construct(public array $args, private ?string $main) {}

        public function getSqlMainReport()
        {
            return $this->main ?? $this->mainOriginal();
        }
    };
}

test('o relatório em JSON consulta a principal e o cabeçalho da instituição ou da escola', function () {
    $report = relatorioEmJson(['instituicao' => 0, 'escola' => null], 'SELECT 1 AS um');

    expect($report->useJson())->toBeTrue()
        ->and($report->getJsonQuery())->toBe('main')
        ->and($report->getSqlHeaderReport())->toContain("CASE WHEN true THEN 'SECRETARIA DE EDUCAÇÃO'")
        ->and(relatorioEmJson(['instituicao' => 1, 'escola' => 2])->getSqlHeaderReport())->toContain('view_dados_escola.cod_escola = 2')
        ->and($report->getJsonData())->toBe(['main' => [['um' => 1, 0 => 1]], 'header' => []]);
});

test('o relatório precisa definir a consulta principal', function () {
    expect(fn () => relatorioEmJson(['instituicao' => 0, 'escola' => 0])->getSqlMainReport())->toThrow(Exception::class, 'Missing implementation.');
});
