<?php

namespace Tests\Unit\Rules;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Rules\CheckMandatoryCensoFields;
use App_Model_LocalFuncionamentoDiferenciado;
use App_Model_TipoMediacaoDidaticoPedagogico;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use iEducar\Modules\Educacenso\Model\EtapaAgregada;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CheckMandatoryCensoFieldsTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test_carga_horaria_total_qualificacao_abaixo_do_minimo_bloqueia()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
        $params->carga_horaria_total = 100;
        $params->tipo_curso_intinerario = 2;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('no mínimo 160 horas', $this->rule->message());
        $this->assertStringContainsString('<b>Carga horária total (em horas)</b>', $this->rule->message());
    }

    public function test_carga_horaria_total_qualificacao_acima_de_800_agora_e_valida()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
        $params->carga_horaria_total = 900;
        $params->tipo_curso_intinerario = 2;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertTrue($result);
    }

    public function test_carga_horaria_total_delega_minimo_fixo_da_etapa_74()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = null;
        $params->etapa_educacenso = 74;
        $params->carga_horaria_total = 2399;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('no mínimo 2400 horas', $this->rule->message());
    }

    public function test_carga_horaria_total_delega_lookup_do_curso_campo_26()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = null;
        $params->etapa_educacenso = 39;
        $params->cod_curso_profissional = 1001; // curso de carga mínima 1200
        $params->carga_horaria_total = 1199;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('no mínimo 1200 horas', $this->rule->message());
    }

    public function test_carga_horaria_total_extrai_fgb_da_organizacao_e_exige_3000()
    {
        $params = $this->createDefaultParams();
        $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . ',' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
        $params->etapa_educacenso = 25;
        $params->tipo_curso_intinerario = 1;
        $params->carga_horaria_total = 2999;

        $result = $this->rule->validaCargaHorariaTotal($params);

        $this->assertFalse($result);
        $this->assertStringContainsString('no mínimo 3000 horas', $this->rule->message());
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

    public function test_horario_minutos_multiplos_de_cinco_passa()
    {
        $params = new \stdClass;
        $params->hora_inicial = '07:00';
        $params->hora_final = '12:30';

        $this->assertTrue($this->rule->validaMinutosHorario($params));
    }

    public function test_horario_de_intervalo_nao_e_validado()
    {
        $params = new \stdClass;
        $params->hora_inicial = '07:00';
        $params->hora_inicio_intervalo = '09:12';
        $params->hora_fim_intervalo = '09:27';
        $params->hora_final = '12:30';

        $this->assertTrue($this->rule->validaMinutosHorario($params));
    }

    public function test_horario_minutos_nao_multiplos_de_cinco_bloqueia()
    {
        $params = new \stdClass;
        $params->hora_inicial = '07:31';

        $this->assertFalse($this->rule->validaMinutosHorario($params));
        $this->assertStringContainsString('hora inicial', $this->rule->message());
        $this->assertStringContainsString('múltiplos de 5', $this->rule->message());
    }

    public function test_horario_vazio_ou_nulo_nao_bloqueia()
    {
        $params = new \stdClass;
        $params->hora_inicial = '';
        $params->hora_final = null;

        $this->assertTrue($this->rule->validaMinutosHorario($params));
    }

    public function test_horario_bordas_dos_minutos()
    {
        $bom = new \stdClass;
        $bom->hora_inicial = '07:55';
        $this->assertTrue($this->rule->validaMinutosHorario($bom));

        $ruim = new \stdClass;
        $ruim->hora_inicial = '07:56';
        $this->assertFalse($this->rule->validaMinutosHorario($ruim));

        $ruimBaixo = new \stdClass;
        $ruimBaixo->hora_inicial = '07:03';
        $this->assertFalse($this->rule->validaMinutosHorario($ruimBaixo));
    }

    public function test_horario_turno_matutino_usa_rotulo_correto()
    {
        $params = new \stdClass;
        $params->hora_final_matutino = '11:22';

        $this->assertFalse($this->rule->validaMinutosHorario($params));
        $this->assertStringContainsString('hora final do turno matutino', $this->rule->message());
    }

    public function test_horario_com_segundos_valida_pelos_minutos()
    {
        $ok = new \stdClass;
        $ok->hora_inicial = '07:30:00';
        $this->assertTrue($this->rule->validaMinutosHorario($ok));

        $nok = new \stdClass;
        $nok->hora_inicial = '07:31:00';
        $this->assertFalse($this->rule->validaMinutosHorario($nok));
    }

    /**
     * Uma turma presencial válida de uma instituição que obriga os campos do
     * censo, numa escola sem local de funcionamento diferenciado.
     */
    private function mandatoryParams(array $overrides = []): \stdClass
    {
        $institution = LegacyInstitutionFactory::new()->create(['obrigar_campos_censo' => true]);

        $params = new \stdClass;
        $params->ref_cod_instituicao = $institution->getKey();
        $params->ref_cod_curso = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $institution, 'modalidade_curso' => ModalidadeCurso::ENSINO_REGULAR])->getKey();
        $params->ref_ref_cod_escola = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $institution, 'local_funcionamento' => '{1}'])->getKey();
        $params->tipo_mediacao_didatico_pedagogico = App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;
        $params->hora_inicial = '08:00';
        $params->hora_final = '12:00';
        $params->hora_inicio_intervalo = '10:00';
        $params->hora_fim_intervalo = '10:15';
        $params->dias_semana = '{2,3,4,5,6}';
        $params->etapa_educacenso = 14;
        $params->etapa_agregada = null;
        $params->organizacao_curricular = null;
        $params->formas_organizacao_turma = FormaOrganizacaoTurma::SERIE_ANO;
        $params->tipo_atendimento = '{0}';
        $params->atividades_complementares = null;
        $params->local_funcionamento_diferenciado = App_Model_LocalFuncionamentoDiferenciado::NAO_ESTA;
        $params->carga_horaria_total = null;
        $params->cod_eixo_curso_profissional = null;

        foreach ($overrides as $field => $value) {
            $params->{$field} = $value;
        }

        return $params;
    }

    public function test_turma_valida_passa_quando_a_instituicao_obriga_os_campos_do_censo(): void
    {
        $this->assertTrue($this->rule->passes('schoolClass', $this->mandatoryParams()));
    }

    public function test_sem_obrigar_os_campos_do_censo_nada_e_validado(): void
    {
        $params = $this->mandatoryParams(['hora_inicial' => null]);
        LegacyInstitution::query()->whereKey($params->ref_cod_instituicao)->update(['obrigar_campos_censo' => false]);

        $this->assertTrue($this->rule->passes('schoolClass', $params));
    }

    #[DataProvider('camposDaTurmaPresencial')]
    public function test_turma_presencial_exige_horarios_e_dias_da_semana(string $field, string $message): void
    {
        $this->assertFalse($this->rule->passes('schoolClass', $this->mandatoryParams([$field => null])));
        $this->assertSame($message, $this->rule->message());
    }

    public static function camposDaTurmaPresencial(): array
    {
        return [
            ['hora_inicial', 'O campo hora inicial é obrigatório'],
            ['hora_final', 'O campo hora final é obrigatório'],
            ['hora_inicio_intervalo', 'O campo hora início intervalo é obrigatório'],
            ['hora_fim_intervalo', 'O campo hora fim intervalo é obrigatório'],
            ['dias_semana', 'O campo dias da semana é obrigatório'],
        ];
    }

    public function test_formacao_geral_basica_exige_a_etapa_de_ensino(): void
    {
        $params = $this->mandatoryParams(['organizacao_curricular' => '{1}', 'etapa_agregada' => EtapaAgregada::ENSINO_MEDIO, 'etapa_educacenso' => null]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertStringContainsString('deve ser obrigatório quando o campo "Organização Curricular da Turma"', $this->rule->message());
    }

    public function test_unidade_socioeducativa_ou_prisional_nao_aceita_as_etapas_iniciais(): void
    {
        $params = $this->mandatoryParams([
            'local_funcionamento_diferenciado' => App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL,
            'etapa_educacenso' => 1,
        ]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertSame('Quando o campo: Local de funcionamento diferenciado é: Unidade prisional, o campo: Etapa de ensino não pode ser nenhuma das seguintes opções: 1, 2, 3 ou 56.', $this->rule->message());
    }

    public function test_turma_com_atividade_complementar_exige_as_atividades(): void
    {
        $params = $this->mandatoryParams(['tipo_atendimento' => '{4}', 'atividades_complementares' => null]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertSame('Campo atividades complementares é obrigatório.', $this->rule->message());
    }

    public function test_turma_a_distancia_ou_semipresencial_precisa_ser_curricular(): void
    {
        $params = $this->mandatoryParams([
            'tipo_atendimento' => '{5}',
            'tipo_mediacao_didatico_pedagogico' => App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA,
        ]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertStringContainsString('Tipo de Turma deve ser: Curricular (etapa de ensino)', $this->rule->message());
    }

    public function test_curso_de_eja_nao_aceita_turma_de_atividade_complementar(): void
    {
        $params = $this->mandatoryParams(['tipo_atendimento' => '{4}', 'atividades_complementares' => '{1}']);
        LegacyCourse::query()->whereKey($params->ref_cod_curso)->update(['modalidade_curso' => ModalidadeCurso::EJA]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertStringContainsString('Educação de Jovens e Adultos (EJA)', $this->rule->message());
    }

    public function test_local_de_funcionamento_diferenciado_depende_do_local_da_escola(): void
    {
        $params = $this->mandatoryParams(['local_funcionamento_diferenciado' => App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertStringContainsString('Unidade de atendimento socioeducativo quando o local de funcionamento da escola', $this->rule->message());

        $params = $this->mandatoryParams(['local_funcionamento_diferenciado' => App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertStringContainsString('Unidade prisional quando o local de funcionamento da escola', $this->rule->message());

        LegacySchool::query()->whereKey($params->ref_ref_cod_escola)->update(['local_funcionamento' => '{9,10}']);

        $this->assertTrue($this->rule->passes('schoolClass', $params));
    }

    public function test_etapa_de_qualificacao_profissional_exige_o_eixo(): void
    {
        $params = $this->mandatoryParams(['etapa_educacenso' => 67, 'cod_eixo_curso_profissional' => '']);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertStringContainsString('Código do eixo do curso de qualificação profissional', $this->rule->message());

        $params->cod_eixo_curso_profissional = 5;

        $this->assertTrue($this->rule->passes('schoolClass', $params));
    }

    public function test_forma_de_organizacao_da_turma_depende_da_etapa(): void
    {
        $params = $this->mandatoryParams(['formas_organizacao_turma' => FormaOrganizacaoTurma::SEMESTRAL, 'etapa_educacenso' => 14]);

        $this->assertFalse($this->rule->passes('schoolClass', $params));
        $this->assertStringContainsString('Não é possível selecionar a opção: <b>Períodos semestrais</b>', $this->rule->message());
    }

    public function test_pela_regra_completa_os_minutos_a_organizacao_curricular_a_combinacao_da_etapa_e_a_carga_horaria_tambem_barram(): void
    {
        $this->assertFalse($this->rule->passes('schoolClass', $this->mandatoryParams(['hora_inicial' => '08:03'])));
        $this->assertStringContainsString('múltiplos de 5', $this->rule->message());

        $this->assertFalse($this->rule->passes('schoolClass', $this->mandatoryParams(['hora_final' => '12h'])));
        $this->assertStringContainsString('hora final deve ter os minutos múltiplos de 5', $this->rule->message());

        $this->assertFalse($this->rule->passes('schoolClass', $this->mandatoryParams(['organizacao_curricular' => '{1}', 'etapa_agregada' => EtapaAgregada::ENSINO_FUNDAMENTAL])));
        $this->assertStringContainsString('"Formação geral básica" só pode ser preenchida', $this->rule->message());

        $this->assertFalse($this->rule->passes('schoolClass', $this->mandatoryParams([
            'tipo_mediacao_didatico_pedagogico' => App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA,
            'etapa_agregada' => EtapaAgregada::EDUCACAO_INFANTIL,
            'etapa_educacenso' => 1,
        ])));
        $this->assertStringContainsString('não se aplica a essa combinação', $this->rule->message());

        $this->assertFalse($this->rule->passes('schoolClass', $this->mandatoryParams(['etapa_educacenso' => 67, 'cod_eixo_curso_profissional' => 5, 'carga_horaria_total' => 10])));
        $this->assertStringContainsString('Carga horária total (em horas)', $this->rule->message());

        $this->assertTrue($this->rule->passes('schoolClass', $this->mandatoryParams(['tipo_atendimento' => null, 'etapa_agregada' => EtapaAgregada::ENSINO_FUNDAMENTAL])));
    }
}
