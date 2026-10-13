<?php

use App\Models\Educacenso\Registro20;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma as Forma;
use iEducar\Modules\Educacenso\Validator\FormaOrganizacaoTurma;
use Tests\TestCase;

uses(TestCase::class);

test('valida a forma de organizacao pela etapa da turma', function (?int $forma, ?int $etapa, bool $valida) {
    $turma = new Registro20;
    $turma->formasOrganizacaoTurma = $forma;
    $turma->etapaEducacenso = $etapa;

    $validator = new FormaOrganizacaoTurma($turma);

    $this->assertSame($valida, $validator->isValid());
    $this->assertSame('Teste', $validator->getMessage());
})->with([
    'sem forma de organização' => [null, 14, true],
    'sem etapa' => [Forma::SERIE_ANO, null, true],
    'educação infantil' => [Forma::CICLOS, 1, true],
    'série/ano no fundamental' => [Forma::SERIE_ANO, 14, true],
    'semestral no fundamental' => [Forma::SEMESTRAL, 14, false],
    'ciclos no curso técnico' => [Forma::CICLOS, 39, false],
    'não seriado na EJA' => [Forma::NAO_SERIADO, 69, true],
    'módulos no ensino médio' => [Forma::MODULES, 25, true],
]);
