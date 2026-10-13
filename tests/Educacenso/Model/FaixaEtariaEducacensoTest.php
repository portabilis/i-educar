<?php

use iEducar\Modules\Educacenso\Model\FaixaEtariaEducacenso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use Tests\TestCase;

uses(TestCase::class);

test('idade no censo usa ano do censo menos ano de nascimento', function () {
    // aluno Rhyan: nascido em 2020, no Censo de 2026 tem 6 anos
    $this->assertSame(6, FaixaEtariaEducacenso::idadeNoCenso(2026, '2020-02-15'));
});

test('idade no censo ignora mes e dia', function () {
    // nascido no fim do ano, ainda conta apenas o ano
    $this->assertSame(6, FaixaEtariaEducacenso::idadeNoCenso(2026, '2020-12-31'));
});

test('idade no censo nula sem data de nascimento', function () {
    $this->assertNull(FaixaEtariaEducacenso::idadeNoCenso(2026, null));
    $this->assertNull(FaixaEtariaEducacenso::idadeNoCenso(2026, ''));
});

test('regra pela etapa de ensino', function () {
    // etapa 18 (5º Ano) permite de 7 a 50 anos
    $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(18, null, null);

    $this->assertSame([7, 50], $regra['faixa']);
    $this->assertSame(FaixaEtariaEducacenso::ORIGEM_ETAPA, $regra['origem']);
});

test('regra nula para etapa sem faixa definida', function () {
    $this->assertNull(FaixaEtariaEducacenso::regraDeIdadeDoAluno(99, null, null));
    $this->assertNull(FaixaEtariaEducacenso::regraDeIdadeDoAluno(null, null, null));
});

test('unidade prisional substitui a faixa da etapa', function () {
    // local prisional (3) usa 18 a 94, mesmo numa etapa que permitiria mais novo
    $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(70, null, 3);

    $this->assertSame([18, 94], $regra['faixa']);
    $this->assertSame(FaixaEtariaEducacenso::ORIGEM_PRISIONAL, $regra['origem']);
});

test('unidade socioeducativa substitui a faixa da etapa', function () {
    $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(70, null, 2);

    $this->assertSame([12, 94], $regra['faixa']);
    $this->assertSame(FaixaEtariaEducacenso::ORIGEM_SOCIOEDUCATIVO, $regra['origem']);
});

test('itinerario sem formacao geral basica substitui a faixa da etapa', function () {
    $organizacao = [OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL];
    $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(39, $organizacao, null);

    $this->assertSame([12, 94], $regra['faixa']);
    $this->assertSame(FaixaEtariaEducacenso::ORIGEM_ITINERARIO_SEM_FORMACAO_GERAL_BASICA, $regra['origem']);
});

test('itinerario com formacao geral basica usa a faixa da etapa', function () {
    // tem itinerário, mas também tem formação geral básica: vale a etapa
    $organizacao = [
        OrganizacaoCurricular::FORMACAO_GERAL_BASICA,
        OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL,
    ];
    $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(39, $organizacao, null);

    $this->assertSame([13, 58], $regra['faixa']);
    $this->assertSame(FaixaEtariaEducacenso::ORIGEM_ETAPA, $regra['origem']);
});

test('faixas das funcoes de gestor e profissional', function () {
    // layout do Censo, bloco por função: gestor 18 a 95, profissional 14 a 95
    $this->assertSame([18, 95], FaixaEtariaEducacenso::FAIXA_GESTOR);
    $this->assertSame([14, 95], FaixaEtariaEducacenso::FAIXA_PROFISSIONAL);
});
