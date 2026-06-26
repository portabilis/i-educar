<?php

namespace Tests\Unit\Rules;

use App\Rules\CheckMandatoryCensoFields;
use App_Model_TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\EtapaAgregada;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use Tests\TestCase;

class CheckMandatoryCensoFieldsTest extends TestCase
{
    private CheckMandatoryCensoFields $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new CheckMandatoryCensoFields;
    }

    private function createDefaultParams(): \stdClass
    {
        $params = new \stdClass;
        $params->ref_cod_instituicao = 1;
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $params->organizacao_curricular = null;
        $params->etapa_educacenso = null;

        return $params;
    }

    public function test_organizacao_curricular_null()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = null;

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_organizacao_curricular_array_vazio()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_organizacao_curricular_null_entre_chaves()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{null}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_formacao_geral_basica_valida_com_ensino_medio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_formacao_geral_basica_valida_com_normal_magisterio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_formacao_geral_basica_invalida_com_etapa_agregada_incorreta()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_FUNDAMENTAL;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('Formação geral básica', $this->rule->message());
        $this->assertStringContainsString('304 ou 305', $this->rule->message());
    }

    public function test_itinerario_aprofundamento_valido_com_ensino_medio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_itinerario_aprofundamento_valido_com_normal_magisterio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_itinerario_aprofundamento_invalido_com_etapa_agregada_incorreta()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_FUNDAMENTAL;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('Itinerário formativo de aprofundamento', $this->rule->message());
        $this->assertStringContainsString('304 ou 305', $this->rule->message());
    }

    public function test_itinerario_tecnico_valido_com_ensino_medio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_itinerario_tecnico_valido_com_normal_magisterio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_itinerario_tecnico_invalido_com_etapa_agregada_incorreta()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_FUNDAMENTAL;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('Itinerário de formação técnica e profissional', $this->rule->message());
        $this->assertStringContainsString('304 ou 305', $this->rule->message());
    }

    public function test_todas_organizacoes_validas_com_ensino_medio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $organizations = OrganizacaoCurricular::FORMACAO_GERAL_BASICA . ',' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . ',' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL;
        $params->organizacao_curricular = '{' . $organizations . '}';

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_etapa_ensino_valida_com_formacao_geral_basica_ensino_medio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
        $params->etapa_educacenso = 25;

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_etapa_ensino_invalida_com_formacao_geral_basica_ensino_medio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
        $params->etapa_educacenso = 30;

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('25, 26, 27, 28 ou 29', $this->rule->message());
    }

    public function test_etapa_ensino_valida_com_formacao_geral_basica_normal_magisterio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
        $params->etapa_educacenso = 35;

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertTrue($result);
    }

    public function test_etapa_ensino_invalida_com_formacao_geral_basica_normal_magisterio()
    {
        $params = $this->createDefaultParams();
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
        $params->etapa_educacenso = 30;

        $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('35, 36, 37 ou 38', $this->rule->message());
    }

    public function test_carga_horaria_total_opcional_quando_iftp_sem_valor()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
        $params->carga_horaria_total = null;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertTrue($result);
    }

    public function test_carga_horaria_total_qualificacao_invalida_fora_da_faixa()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
        $params->carga_horaria_total = 100;
        $params->tipo_curso_intinerario = 2;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('160 e 800', $this->rule->message());
    }

    public function test_carga_horaria_total_qualificacao_valida_dentro_da_faixa()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
        $params->carga_horaria_total = 400;
        $params->tipo_curso_intinerario = 2;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertTrue($result);
    }

    private function paramsTurma(int $mediacao, string $tipoAtendimento, ?int $etapaAgregada, ?string $organizacao, $etapa): \stdClass
    {
        $params = new \stdClass;
        $params->tipo_mediacao_didatico_pedagogico = $mediacao;
        $params->tipo_atendimento = $tipoAtendimento;
        $params->etapa_agregada = $etapaAgregada;
        $params->organizacao_curricular = $organizacao;
        $params->etapa_educacenso = $etapa;

        return $params;
    }

    public function test_save_impedimento_2_ead_com_educacao_infantil_bloqueia()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA, '{0}', EtapaAgregada::EDUCACAO_INFANTIL, null, 1);

        $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
        $this->assertStringContainsString('Educação a distância', $this->rule->message());
        $this->assertStringContainsString('não se aplica a essa combinação', $this->rule->message());
    }

    public function test_save_impedimento_3_semipresencial_eja_com_etapa_invalida_bloqueia()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 14);

        $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
        $this->assertStringContainsString('Semipresencial', $this->rule->message());
        $this->assertStringContainsString('69, 70, 71 ou 72', $this->rule->message());
    }

    public function test_save_semipresencial_eja_com_etapa_valida_passa()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 70);

        $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
    }

    public function test_save_ead_eja_com_etapa_valida_passa()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 71);

        $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
    }

    public function test_save_ead_eja_com_etapa_da_uniao_de_outra_agregada_bloqueia()
    {
        // 25 é válida para EAD+304 (com FGB), mas NÃO para EAD+306 (EJA): a união antiga deixava passar.
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 25);

        $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
        $this->assertStringContainsString('71, 74 ou 67', $this->rule->message());
    }

    public function test_save_presencial_ensino_medio_sem_fgb_bloqueia()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{0}', EtapaAgregada::ENSINO_MEDIO, null, 25);

        $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
        $this->assertStringContainsString('Formação geral básica', $this->rule->message());
    }

    public function test_save_presencial_ensino_medio_com_fgb_passa()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{0}', EtapaAgregada::ENSINO_MEDIO, '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}', 25);

        $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
    }

    public function test_save_tipo_turma_curricular_com_atividade_complementar_multi_bloqueia_56()
    {
        // Curricular com Atividade Complementar + Multi (303) permite só 22 e 23 (não 56).
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{9}', EtapaAgregada::MULTI_CORRECAO_FLUXO, null, 56);

        $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
        $this->assertStringContainsString('com Atividade Complementar', $this->rule->message());
        $this->assertStringContainsString('22 ou 23', $this->rule->message());
    }

    public function test_save_etapa_vazia_passa()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{0}', EtapaAgregada::ENSINO_MEDIO, null, null);

        $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
    }

    public function test_save_tipo_turma_nao_curricular_passa()
    {
        // AEE ({5}) não possui etapa de ensino: a validação por combinação não se aplica.
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{5}', EtapaAgregada::EDUCACAO_INFANTIL, null, 1);

        $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
    }

    public function test_save_curricular_com_atividade_complementar_multi_etapa_valida_passa()
    {
        $params = $this->paramsTurma(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{9}', EtapaAgregada::MULTI_CORRECAO_FLUXO, null, 22);

        $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
    }
}
