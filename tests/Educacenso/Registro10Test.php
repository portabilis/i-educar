<?php

use App\Models\Educacenso\Registro10;
use Database\Factories\LegacySchoolClassFactory;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\InstrumentosPedagogicos;
use iEducar\Modules\Educacenso\Model\LinguaMinistrada;
use iEducar\Modules\Educacenso\Model\OrganizacaoEnsino;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Validator\School\HasDifferentStepsOfChildEducationValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('nenhum junto de outra opcao e preenchimento incorreto', function () {
    $registro = new Registro10;
    $registro->equipamentos = [Equipamentos::NENHUM_EQUIPAMENTO_LISTADO, 1];
    $registro->redeLocal = [RedeLocal::NENHUMA, RedeLocal::A_CABO];
    $registro->instrumentosPedagogicos = [InstrumentosPedagogicos::NENHUM_DOS_INSTRUMENTOS_LISTADOS, 1];

    $this->assertTrue($registro->EquipamentosPreenchidosIncorretamente());
    $this->assertTrue($registro->redeLocalInexistenteEOutrosCamposPreenchidos());
    $this->assertTrue($registro->instrumentosPedagogicosPreenchidosIncorretamente());

    $registro->equipamentos = [Equipamentos::NENHUM_EQUIPAMENTO_LISTADO];
    $registro->instrumentosPedagogicos = [1];

    $this->assertFalse($registro->EquipamentosPreenchidosIncorretamente());
    $this->assertFalse($registro->instrumentosPedagogicosPreenchidosIncorretamente());
});

test('computadores profissionais e equipamentos contam quando algum foi informado', function () {
    $registro = new Registro10;

    $this->assertTrue($registro->quantidadeComputadoresAlunosNaoPreenchida());
    $this->assertFalse($registro->possuiComputadoresDeMesaTabletsEPortateis());
    $this->assertFalse((bool) $registro->quantidadeProfissionaisPreenchida());
    $this->assertFalse((bool) $registro->existeEquipamentos());

    $registro->quantidadeComputadoresAlunosTablets = 3;
    $registro->qtdOrientadorComunitario = 1;
    $registro->impressorasMultifuncionais = 2;

    $this->assertFalse($registro->quantidadeComputadoresAlunosNaoPreenchida());
    $this->assertTrue($registro->possuiComputadoresDeMesaTabletsEPortateis());
    $this->assertTrue((bool) $registro->quantidadeProfissionaisPreenchida());
    $this->assertTrue((bool) $registro->existeEquipamentos());
});

test('reconhece a organizacao do ensino as cotas e a lingua portuguesa', function () {
    $registro = new Registro10;
    $registro->organizacaoEnsino = [OrganizacaoEnsino::SERIE_ANO, OrganizacaoEnsino::MODULOS];
    $registro->reservaVagasCotas = [ReservaVagasCotas::PCD, ReservaVagasCotas::OUTROS];
    $registro->linguaMinistrada = LinguaMinistrada::INDIGENA_E_PORTUGUESA;

    $this->assertTrue($registro->organizacaoEnsinoSerieAno());
    $this->assertFalse($registro->organizacaoEnsinoPeriodosSemestrais());
    $this->assertFalse($registro->organizacaoEnsinoCliclosEnsinoFundamental());
    $this->assertFalse($registro->organizacaoEnsinoGruposNaoSeriados());
    $this->assertTrue($registro->organizacaoEnsinoModulos());
    $this->assertFalse($registro->reservaVagasCotasNaoPossui());
    $this->assertFalse($registro->reservaVagasCotasAutodeclaracaoPpi());
    $this->assertFalse($registro->reservaVagasCotasCondicaoRenda());
    $this->assertFalse($registro->reservaVagasCotasEscolaPublica());
    $this->assertTrue($registro->reservaVagasCotasPcd());
    $this->assertTrue($registro->reservaVagasCotasOutros());
    $this->assertTrue($registro->linguaMinistradaPortugues());
});

test('a escola precisa de turma fora da educacao infantil', function () {
    $infantil = LegacySchoolClassFactory::new()->create(['etapa_educacenso' => 1]);

    $registro = new Registro10;
    $registro->codEscola = $infantil->ref_ref_cod_escola;

    $this->assertFalse($registro->HasDifferentStepsOfChildEducation());
    $this->assertSame('Não existem turmas diferentes de Educação infantil nesta escola', (new HasDifferentStepsOfChildEducationValidator($registro->codEscola))->getMessage());

    LegacySchoolClassFactory::new()->create($infantil->only(['ref_ref_cod_escola', 'ref_ref_cod_serie', 'ref_cod_curso', 'ref_cod_instituicao']) + ['etapa_educacenso' => 14]);

    $this->assertTrue($registro->HasDifferentStepsOfChildEducation());
});
