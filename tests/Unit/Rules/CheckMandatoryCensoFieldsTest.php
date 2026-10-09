<?php

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Rules\CheckMandatoryCensoFields;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use iEducar\Modules\Educacenso\Model\EtapaAgregada;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->rule = new CheckMandatoryCensoFields;

    $this->createDefaultParams = function (): stdClass {
        $params = new stdClass;
        $params->ref_cod_instituicao = 1;
        $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
        $params->organizacao_curricular = null;
        $params->etapa_educacenso = null;

        return $params;
    };

    $this->paramsTurma = function (int $mediacao, string $tipoAtendimento, ?int $etapaAgregada, ?string $organizacao, $etapa): stdClass {
        $params = new stdClass;
        $params->tipo_mediacao_didatico_pedagogico = $mediacao;
        $params->tipo_atendimento = $tipoAtendimento;
        $params->etapa_agregada = $etapaAgregada;
        $params->organizacao_curricular = $organizacao;
        $params->etapa_educacenso = $etapa;

        return $params;
    };

    /**
     * Uma turma presencial válida de uma instituição que obriga os campos do
     * censo, numa escola sem local de funcionamento diferenciado.
     */
    $this->mandatoryParams = function (array $overrides = []): stdClass {
        $institution = LegacyInstitutionFactory::new()->create(['obrigar_campos_censo' => true]);

        $params = new stdClass;
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
    };
});

test('organizacao curricular null', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = null;

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('organizacao curricular array vazio', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('organizacao curricular null entre chaves', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{null}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('formacao geral basica valida com ensino medio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('formacao geral basica valida com normal magisterio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('formacao geral basica invalida com etapa agregada incorreta', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_FUNDAMENTAL;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('Formação geral básica', $this->rule->message());
    $this->assertStringContainsString('304 ou 305', $this->rule->message());
});

test('itinerario aprofundamento valido com ensino medio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('itinerario aprofundamento valido com normal magisterio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('itinerario aprofundamento invalido com etapa agregada incorreta', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_FUNDAMENTAL;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('Itinerário formativo de aprofundamento', $this->rule->message());
    $this->assertStringContainsString('304 ou 305', $this->rule->message());
});

test('itinerario tecnico valido com ensino medio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('itinerario tecnico valido com normal magisterio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('itinerario tecnico invalido com etapa agregada incorreta', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_FUNDAMENTAL;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('Itinerário de formação técnica e profissional', $this->rule->message());
    $this->assertStringContainsString('304 ou 305', $this->rule->message());
});

test('todas organizacoes validas com ensino medio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
    $organizations = OrganizacaoCurricular::FORMACAO_GERAL_BASICA . ',' . OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO . ',' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL;
    $params->organizacao_curricular = '{' . $organizations . '}';

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('etapa ensino valida com formacao geral basica ensino medio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->etapa_educacenso = 25;

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('etapa ensino invalida com formacao geral basica ensino medio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->etapa_educacenso = 30;

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('25, 26, 27, 28 ou 29', $this->rule->message());
});

test('etapa ensino valida com formacao geral basica normal magisterio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->etapa_educacenso = 35;

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertTrue($result);
});

test('etapa ensino invalida com formacao geral basica normal magisterio', function () {
    $params = ($this->createDefaultParams)();
    $params->etapa_agregada = EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO;
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->etapa_educacenso = 30;

    $result = $this->rule->validaCampoOrganizacaoCurricularDaTurma($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('35, 36, 37 ou 38', $this->rule->message());
});

test('carga horaria total opcional quando iftp sem valor', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
    $params->carga_horaria_total = null;

    $result = $this->rule->validaCargaHorariaTotal($params);

    $this->assertTrue($result);
});

test('carga horaria total qualificacao abaixo do minimo bloqueia', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
    $params->carga_horaria_total = 100;
    $params->tipo_curso_intinerario = 2;

    $result = $this->rule->validaCargaHorariaTotal($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('no mínimo 160 horas', $this->rule->message());
    $this->assertStringContainsString('<b>Carga horária total (em horas)</b>', $this->rule->message());
});

test('carga horaria total qualificacao acima de 800 agora e valida', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
    $params->carga_horaria_total = 900;
    $params->tipo_curso_intinerario = 2;

    $result = $this->rule->validaCargaHorariaTotal($params);

    $this->assertTrue($result);
});

test('carga horaria total delega minimo fixo da etapa 74', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = null;
    $params->etapa_educacenso = 74;
    $params->carga_horaria_total = 2399;

    $result = $this->rule->validaCargaHorariaTotal($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('no mínimo 2400 horas', $this->rule->message());
});

test('carga horaria total delega lookup do curso campo 26', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = null;
    $params->etapa_educacenso = 39;
    $params->cod_curso_profissional = 1001; // curso de carga mínima 1200
    $params->carga_horaria_total = 1199;

    $result = $this->rule->validaCargaHorariaTotal($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('no mínimo 1200 horas', $this->rule->message());
});

test('carga horaria total extrai fgb da organizacao e exige 3000', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . ',' . OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL . '}';
    $params->etapa_educacenso = 25;
    $params->tipo_curso_intinerario = 1;
    $params->carga_horaria_total = 2999;

    $result = $this->rule->validaCargaHorariaTotal($params);

    $this->assertFalse($result);
    $this->assertStringContainsString('no mínimo 3000 horas', $this->rule->message());
});

test('save impedimento 2 ead com educacao infantil bloqueia', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA, '{0}', EtapaAgregada::EDUCACAO_INFANTIL, null, 1);

    $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
    $this->assertStringContainsString('Educação a distância', $this->rule->message());
    $this->assertStringContainsString('não se aplica a essa combinação', $this->rule->message());
});

test('save impedimento 3 semipresencial eja com etapa invalida bloqueia', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 14);

    $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
    $this->assertStringContainsString('Semipresencial', $this->rule->message());
    $this->assertStringContainsString('69, 70, 71 ou 72', $this->rule->message());
});

test('save semipresencial eja com etapa valida passa', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 70);

    $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
});

test('save ead eja com etapa valida passa', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 71);

    $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
});

test('save ead eja com etapa da uniao de outra agregada bloqueia', function () {
    // 25 é válida para EAD+304 (com FGB), mas NÃO para EAD+306 (EJA): a união antiga deixava passar.
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA, '{0}', EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, null, 25);

    $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
    $this->assertStringContainsString('71, 74 ou 67', $this->rule->message());
});

test('save presencial ensino medio sem fgb bloqueia', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{0}', EtapaAgregada::ENSINO_MEDIO, null, 25);

    $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
    $this->assertStringContainsString('Formação geral básica', $this->rule->message());
});

test('save presencial ensino medio com fgb passa', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{0}', EtapaAgregada::ENSINO_MEDIO, '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}', 25);

    $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
});

test('save tipo turma curricular com atividade complementar multi bloqueia 56', function () {
    // Curricular com Atividade Complementar + Multi (303) permite só 22 e 23 (não 56).
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{9}', EtapaAgregada::MULTI_CORRECAO_FLUXO, null, 56);

    $this->assertFalse($this->rule->validaEtapaEnsinoPorCombinacao($params));
    $this->assertStringContainsString('com Atividade Complementar', $this->rule->message());
    $this->assertStringContainsString('22 ou 23', $this->rule->message());
});

test('save etapa vazia passa', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{0}', EtapaAgregada::ENSINO_MEDIO, null, null);

    $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
});

test('save tipo turma nao curricular passa', function () {
    // AEE ({5}) não possui etapa de ensino: a validação por combinação não se aplica.
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{5}', EtapaAgregada::EDUCACAO_INFANTIL, null, 1);

    $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
});

test('save curricular com atividade complementar multi etapa valida passa', function () {
    $params = ($this->paramsTurma)(App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, '{9}', EtapaAgregada::MULTI_CORRECAO_FLUXO, null, 22);

    $this->assertTrue($this->rule->validaEtapaEnsinoPorCombinacao($params));
});

test('horario minutos multiplos de cinco passa', function () {
    $params = new stdClass;
    $params->hora_inicial = '07:00';
    $params->hora_final = '12:30';

    $this->assertTrue($this->rule->validaMinutosHorario($params));
});

test('horario de intervalo nao e validado', function () {
    $params = new stdClass;
    $params->hora_inicial = '07:00';
    $params->hora_inicio_intervalo = '09:12';
    $params->hora_fim_intervalo = '09:27';
    $params->hora_final = '12:30';

    $this->assertTrue($this->rule->validaMinutosHorario($params));
});

test('horario minutos nao multiplos de cinco bloqueia', function () {
    $params = new stdClass;
    $params->hora_inicial = '07:31';

    $this->assertFalse($this->rule->validaMinutosHorario($params));
    $this->assertStringContainsString('hora inicial', $this->rule->message());
    $this->assertStringContainsString('múltiplos de 5', $this->rule->message());
});

test('horario vazio ou nulo nao bloqueia', function () {
    $params = new stdClass;
    $params->hora_inicial = '';
    $params->hora_final = null;

    $this->assertTrue($this->rule->validaMinutosHorario($params));
});

test('horario bordas dos minutos', function () {
    $bom = new stdClass;
    $bom->hora_inicial = '07:55';
    $this->assertTrue($this->rule->validaMinutosHorario($bom));

    $ruim = new stdClass;
    $ruim->hora_inicial = '07:56';
    $this->assertFalse($this->rule->validaMinutosHorario($ruim));

    $ruimBaixo = new stdClass;
    $ruimBaixo->hora_inicial = '07:03';
    $this->assertFalse($this->rule->validaMinutosHorario($ruimBaixo));
});

test('horario turno matutino usa rotulo correto', function () {
    $params = new stdClass;
    $params->hora_final_matutino = '11:22';

    $this->assertFalse($this->rule->validaMinutosHorario($params));
    $this->assertStringContainsString('hora final do turno matutino', $this->rule->message());
});

test('horario com segundos valida pelos minutos', function () {
    $ok = new stdClass;
    $ok->hora_inicial = '07:30:00';
    $this->assertTrue($this->rule->validaMinutosHorario($ok));

    $nok = new stdClass;
    $nok->hora_inicial = '07:31:00';
    $this->assertFalse($this->rule->validaMinutosHorario($nok));
});

test('turma valida passa quando a instituicao obriga os campos do censo', function () {
    $this->assertTrue($this->rule->passes('schoolClass', ($this->mandatoryParams)()));
});

test('sem obrigar os campos do censo nada e validado', function () {
    $params = ($this->mandatoryParams)(['hora_inicial' => null]);
    LegacyInstitution::query()->whereKey($params->ref_cod_instituicao)->update(['obrigar_campos_censo' => false]);

    $this->assertTrue($this->rule->passes('schoolClass', $params));
});

test('turma presencial exige horarios e dias da semana', function (string $field, string $message) {
    $this->assertFalse($this->rule->passes('schoolClass', ($this->mandatoryParams)([$field => null])));
    $this->assertSame($message, $this->rule->message());
})->with([
    ['hora_inicial', 'O campo hora inicial é obrigatório'],
    ['hora_final', 'O campo hora final é obrigatório'],
    ['hora_inicio_intervalo', 'O campo hora início intervalo é obrigatório'],
    ['hora_fim_intervalo', 'O campo hora fim intervalo é obrigatório'],
    ['dias_semana', 'O campo dias da semana é obrigatório'],
]);

test('formacao geral basica exige a etapa de ensino', function () {
    $params = ($this->mandatoryParams)(['organizacao_curricular' => '{1}', 'etapa_agregada' => EtapaAgregada::ENSINO_MEDIO, 'etapa_educacenso' => null]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertStringContainsString('deve ser obrigatório quando o campo "Organização Curricular da Turma"', $this->rule->message());
});

test('unidade socioeducativa ou prisional nao aceita as etapas iniciais', function () {
    $params = ($this->mandatoryParams)([
        'local_funcionamento_diferenciado' => App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL,
        'etapa_educacenso' => 1,
    ]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertSame('Quando o campo: Local de funcionamento diferenciado é: Unidade prisional, o campo: Etapa de ensino não pode ser nenhuma das seguintes opções: 1, 2, 3 ou 56.', $this->rule->message());
});

test('turma com atividade complementar exige as atividades', function () {
    $params = ($this->mandatoryParams)(['tipo_atendimento' => '{4}', 'atividades_complementares' => null]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertSame('Campo atividades complementares é obrigatório.', $this->rule->message());
});

test('turma a distancia ou semipresencial precisa ser curricular', function () {
    $params = ($this->mandatoryParams)([
        'tipo_atendimento' => '{5}',
        'tipo_mediacao_didatico_pedagogico' => App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA,
    ]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertStringContainsString('Tipo de Turma deve ser: Curricular (etapa de ensino)', $this->rule->message());
});

test('curso de eja nao aceita turma de atividade complementar', function () {
    $params = ($this->mandatoryParams)(['tipo_atendimento' => '{4}', 'atividades_complementares' => '{1}']);
    LegacyCourse::query()->whereKey($params->ref_cod_curso)->update(['modalidade_curso' => ModalidadeCurso::EJA]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertStringContainsString('Educação de Jovens e Adultos (EJA)', $this->rule->message());
});

test('local de funcionamento diferenciado depende do local da escola', function () {
    $params = ($this->mandatoryParams)(['local_funcionamento_diferenciado' => App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertStringContainsString('Unidade de atendimento socioeducativo quando o local de funcionamento da escola', $this->rule->message());

    $params = ($this->mandatoryParams)(['local_funcionamento_diferenciado' => App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertStringContainsString('Unidade prisional quando o local de funcionamento da escola', $this->rule->message());

    LegacySchool::query()->whereKey($params->ref_ref_cod_escola)->update(['local_funcionamento' => '{9,10}']);

    $this->assertTrue($this->rule->passes('schoolClass', $params));
});

test('etapa de qualificacao profissional exige o eixo', function () {
    $params = ($this->mandatoryParams)(['etapa_educacenso' => 67, 'cod_eixo_curso_profissional' => '']);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertStringContainsString('Código do eixo do curso de qualificação profissional', $this->rule->message());

    $params->cod_eixo_curso_profissional = 5;

    $this->assertTrue($this->rule->passes('schoolClass', $params));
});

test('forma de organizacao da turma depende da etapa', function () {
    $params = ($this->mandatoryParams)(['formas_organizacao_turma' => FormaOrganizacaoTurma::SEMESTRAL, 'etapa_educacenso' => 14]);

    $this->assertFalse($this->rule->passes('schoolClass', $params));
    $this->assertStringContainsString('Não é possível selecionar a opção: <b>Períodos semestrais</b>', $this->rule->message());
});

test('pela regra completa os minutos a organizacao curricular a combinacao da etapa e a carga horaria tambem barram', function () {
    $this->assertFalse($this->rule->passes('schoolClass', ($this->mandatoryParams)(['hora_inicial' => '08:03'])));
    $this->assertStringContainsString('múltiplos de 5', $this->rule->message());

    $this->assertFalse($this->rule->passes('schoolClass', ($this->mandatoryParams)(['hora_final' => '12h'])));
    $this->assertStringContainsString('hora final deve ter os minutos múltiplos de 5', $this->rule->message());

    $this->assertFalse($this->rule->passes('schoolClass', ($this->mandatoryParams)(['organizacao_curricular' => '{1}', 'etapa_agregada' => EtapaAgregada::ENSINO_FUNDAMENTAL])));
    $this->assertStringContainsString('"Formação geral básica" só pode ser preenchida', $this->rule->message());

    $this->assertFalse($this->rule->passes('schoolClass', ($this->mandatoryParams)([
        'tipo_mediacao_didatico_pedagogico' => App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA,
        'etapa_agregada' => EtapaAgregada::EDUCACAO_INFANTIL,
        'etapa_educacenso' => 1,
    ])));
    $this->assertStringContainsString('não se aplica a essa combinação', $this->rule->message());

    $this->assertFalse($this->rule->passes('schoolClass', ($this->mandatoryParams)(['etapa_educacenso' => 67, 'cod_eixo_curso_profissional' => 5, 'carga_horaria_total' => 10])));
    $this->assertStringContainsString('Carga horária total (em horas)', $this->rule->message());

    $this->assertTrue($this->rule->passes('schoolClass', ($this->mandatoryParams)(['tipo_atendimento' => null, 'etapa_agregada' => EtapaAgregada::ENSINO_FUNDAMENTAL])));
});

test('hora sem minutos com dois digitos nao e multipla de cinco', function () {
    $rule = new CheckMandatoryCensoFields;
    $check = fn ($hora) => (fn () => $this->minutosMultiplosDeCinco($hora))->call($rule);

    $this->assertFalse($check('10'));
    $this->assertFalse($check('10:5'));
    $this->assertTrue($check('10:05'));
});
