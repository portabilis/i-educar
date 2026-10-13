<?php

use Database\Factories\LegacySchoolClassFactory;
use iEducar\Modules\Educacenso\Migrations\UpdateEducacensoInstitutionToLayout2020;
use iEducar\Modules\Educacenso\Migrations\UpdateSchoolClassToLayout2020;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('o layout 2020 tira a atividade complementar extinta das turmas', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    DB::table('pmieducar.turma')->where('cod_turma', $schoolClass->getKey())->update(['atividades_complementares' => '{13108,11002}']);

    UpdateSchoolClassToLayout2020::execute();

    $this->assertSame('{11002}', DB::table('pmieducar.turma')->where('cod_turma', $schoolClass->getKey())->value('atividades_complementares'));
});

test('o layout 2020 atualiza as instituicoes de ensino superior', function () {
    UpdateEducacensoInstitutionToLayout2020::execute();

    $this->assertTrue(DB::table('modules.educacenso_ies')->exists());
});
