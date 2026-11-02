<?php

use App\Services\SebExport\EducacensoStepConverter;
use App\Services\SebExport\ExportService;
use Tests\TestCase;

uses(TestCase::class);

test('a linha da exportação traz mãe, pai e responsável na ordem e completa os que faltam', function () {
    $converter = Mockery::mock(EducacensoStepConverter::class);
    $converter->shouldReceive('convert')->with(14)->andReturn('EF1');
    $service = new ExportService($converter);
    $line = fn (array $record) => (fn () => $this->makeLine((object) $record))->call($service);
    $base = ['ano' => 2030, 'inep_escola' => 123, 'cpf' => '111', 'nome_social' => 'Ana', 'cod_matricula' => 9, 'etapa_educacenso' => 14, 'emancipado' => false];

    expect($line($base + ['cpf_mae' => null, 'cpf_pai' => '222', 'cpf_responsavel' => null]))->toBe('2030;123;111;Ana;9;EF1;0;222;2;;;;')
        ->and($line(['emancipado' => true] + $base + ['cpf_mae' => '333', 'cpf_pai' => '222', 'cpf_responsavel' => '444']))->toBe('2030;123;111;Ana;9;EF1;1;333;1;222;2;444;3');
});
