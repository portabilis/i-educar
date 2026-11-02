<?php

use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStageTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('get quantidade de modulos matricula curso ano nao padrao', function () {
    $turma = LegacySchoolClassFactory::new()->create();
    $etapa = LegacyStageTypeFactory::new()->create();

    foreach (range(1, 4) as $sequencial) {
        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $turma,
            'ref_cod_modulo' => $etapa,
            'sequencial' => $sequencial,
        ]);
    }

    $matricula = [
        'ref_cod_turma' => $turma->getKey(),
        'padrao_ano_escolar' => 0,
    ];

    $this->assertEquals(4, App_Model_IedFinder::getQuantidadeDeModulosMatricula(1, $matricula));
});
