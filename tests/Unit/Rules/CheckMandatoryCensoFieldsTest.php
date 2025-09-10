<?php

namespace Tests\Unit\Rules;

use App\Rules\CheckMandatoryCensoFields;
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
}
