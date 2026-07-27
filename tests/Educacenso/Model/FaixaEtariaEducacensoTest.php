<?php

namespace Tests\Educacenso\Model;

use iEducar\Modules\Educacenso\Model\FaixaEtariaEducacenso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use Tests\TestCase;

class FaixaEtariaEducacensoTest extends TestCase
{
    public function test_idade_no_censo_usa_ano_do_censo_menos_ano_de_nascimento()
    {
        // aluno Rhyan: nascido em 2020, no Censo de 2026 tem 6 anos
        $this->assertSame(6, FaixaEtariaEducacenso::idadeNoCenso(2026, '2020-02-15'));
    }

    public function test_idade_no_censo_ignora_mes_e_dia()
    {
        // nascido no fim do ano, ainda conta apenas o ano
        $this->assertSame(6, FaixaEtariaEducacenso::idadeNoCenso(2026, '2020-12-31'));
    }

    public function test_idade_no_censo_nula_sem_data_de_nascimento()
    {
        $this->assertNull(FaixaEtariaEducacenso::idadeNoCenso(2026, null));
        $this->assertNull(FaixaEtariaEducacenso::idadeNoCenso(2026, ''));
    }

    public function test_regra_pela_etapa_de_ensino()
    {
        // etapa 18 (5º Ano) permite de 7 a 50 anos
        $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(18, null, null);

        $this->assertSame([7, 50], $regra['faixa']);
        $this->assertSame(FaixaEtariaEducacenso::ORIGEM_ETAPA, $regra['origem']);
    }

    public function test_regra_nula_para_etapa_sem_faixa_definida()
    {
        $this->assertNull(FaixaEtariaEducacenso::regraDeIdadeDoAluno(99, null, null));
        $this->assertNull(FaixaEtariaEducacenso::regraDeIdadeDoAluno(null, null, null));
    }

    public function test_unidade_prisional_substitui_a_faixa_da_etapa()
    {
        // local prisional (3) usa 18 a 94, mesmo numa etapa que permitiria mais novo
        $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(70, null, 3);

        $this->assertSame([18, 94], $regra['faixa']);
        $this->assertSame(FaixaEtariaEducacenso::ORIGEM_PRISIONAL, $regra['origem']);
    }

    public function test_unidade_socioeducativa_substitui_a_faixa_da_etapa()
    {
        $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(70, null, 2);

        $this->assertSame([12, 94], $regra['faixa']);
        $this->assertSame(FaixaEtariaEducacenso::ORIGEM_SOCIOEDUCATIVO, $regra['origem']);
    }

    public function test_itinerario_sem_formacao_geral_basica_substitui_a_faixa_da_etapa()
    {
        $organizacao = [OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL];
        $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(39, $organizacao, null);

        $this->assertSame([12, 94], $regra['faixa']);
        $this->assertSame(FaixaEtariaEducacenso::ORIGEM_ITINERARIO_SEM_FORMACAO_GERAL_BASICA, $regra['origem']);
    }

    public function test_itinerario_com_formacao_geral_basica_usa_a_faixa_da_etapa()
    {
        // tem itinerário, mas também tem formação geral básica: vale a etapa
        $organizacao = [
            OrganizacaoCurricular::FORMACAO_GERAL_BASICA,
            OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL,
        ];
        $regra = FaixaEtariaEducacenso::regraDeIdadeDoAluno(39, $organizacao, null);

        $this->assertSame([13, 58], $regra['faixa']);
        $this->assertSame(FaixaEtariaEducacenso::ORIGEM_ETAPA, $regra['origem']);
    }

    public function test_faixas_das_funcoes_de_gestor_e_profissional()
    {
        // layout do Censo, bloco por função: gestor 18 a 95, profissional 14 a 95
        $this->assertSame([18, 95], FaixaEtariaEducacenso::FAIXA_GESTOR);
        $this->assertSame([14, 95], FaixaEtariaEducacenso::FAIXA_PROFISSIONAL);
    }
}
