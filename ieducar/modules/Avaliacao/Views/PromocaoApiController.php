<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyCourse;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Models\View\Discipline;
use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;
use Illuminate\Support\Facades\Auth;

class PromocaoApiController extends ApiCoreController
{
    protected $_dataMapper = 'Avaliacao_Model_NotaComponenteDataMapper';

    protected $_processoAp = 644;

    protected function canAcceptRequest()
    {
        return parent::canAcceptRequest() &&
            $this->validatesPresenceOf('ano');
    }

    protected function canDeleteOldComponentesCurriculares()
    {
        return $this->validatesPresenceOf('ano');
    }

    protected function canPostPromocaoMatricula()
    {
        return $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('matricula_id');
    }

    protected function canGetQuantidadeMatriculas()
    {
        return $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('ano');
    }

    protected function loadNextMatriculaId($currentMatriculaId)
    {
        $escolaId = $this->getRequest()->escola == '' ? 0 : $this->getRequest()->escola;
        $cursoId = empty($this->getRequest()->curso) ? 0 : $this->getRequest()->curso;
        $serieId = empty($this->getRequest()->serie) ? 0 : $this->getRequest()->serie;
        $turmaId = empty($this->getRequest()->turma) ? 0 : $this->getRequest()->turma;
        $matricula = empty($this->getRequest()->matricula) ? 10 : $this->getRequest()->matricula;
        $regraDeAvaliacao = empty($this->getRequest()->regras_avaliacao_id) ? 0 : $this->getRequest()->regras_avaliacao_id;

        $sql = 'SELECT m.cod_matricula FROM pmieducar.matricula AS m
                INNER JOIN pmieducar.aluno ON aluno.cod_aluno = m.ref_cod_aluno
                INNER JOIN pmieducar.matricula_turma AS mt ON m.cod_matricula = mt.ref_cod_matricula
                INNER JOIN pmieducar.serie as s on m.ref_ref_cod_serie = s.cod_serie
                INNER JOIN modules.regra_avaliacao_serie_ano as ra on ra.serie_id = s.cod_serie and ra.ano_letivo = m.ano
                WHERE m.ano = $1
                AND m.ativo = 1
                AND mt.ref_cod_matricula = m.cod_matricula
                AND mt.ativo = 1
                AND ref_cod_matricula > $2
                AND (CASE WHEN $3 = 0  THEN TRUE ELSE $3 = m.ref_ref_cod_escola END)
                AND (CASE WHEN $4 = 0  THEN TRUE ELSE $4 = m.ref_cod_curso END)
                AND (CASE WHEN $5 = 0  THEN TRUE ELSE $5 = m.ref_ref_cod_serie END)
                AND (CASE WHEN $6 = 0  THEN TRUE ELSE $6 = mt.ref_cod_turma END)
                AND (CASE WHEN $7 = 10 THEN TRUE WHEN $7 = 9  THEN m.aprovado NOT IN (4,6) ELSE $7 = m.aprovado END)
                AND (CASE WHEN $8 = 0  THEN TRUE ELSE $8 = ra.regra_avaliacao_id END)
                ORDER BY ref_cod_matricula LIMIT 1';

        $options = [
            'params' => [$this->getRequest()->ano, $currentMatriculaId, $escolaId, $cursoId, $serieId, $turmaId, $matricula, $regraDeAvaliacao],
            'return_only' => 'first-field',
        ];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
    }

    public function loadSituacaoArmazenadaMatricula($matriculaId)
    {
        $sql = 'SELECT aprovado
                  FROM pmieducar.matricula
                 WHERE cod_matricula = $1
                 LIMIT 1';

        $options = ['params' => $matriculaId, 'return_only' => 'first-field'];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
    }

    protected function loadComponentesCurriculares(LegacyRegistration $registration)
    {
        return Discipline::getBySchoolClassAndGrade(
            $registration->schoolClass->cod_turma,
            $registration->ref_ref_cod_serie)
            ->pluck('nome', 'id');
    }

    protected function trySaveBoletimService()
    {
        try {
            // FIXME #parameters
            $this->boletimService()->save();
        } catch (CoreExt_Service_Exception) {
            // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
            // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
        }
    }

    protected function boletimService($reload = false, $build = false, $params = [])
    {
        $matriculaId = $this->matriculaId();

        if (!isset($this->_boletimServices)) {
            $this->_boletimServices = [];
        }

        if ($build) {
            $data = [
                'matricula' => $params['matricula'],
                'usuario' => $params['user_id'],
                'etapa' => $params['etapa'],
                'updateScore' => $params['updateScore'],
            ];
            $this->_boletimServices[$matriculaId] = new Avaliacao_Service_Boletim($data);

            return $this->_boletimServices[$matriculaId];
        }

        if (!isset($this->_boletimServices[$matriculaId]) || $reload) {
            // set service
            try {
                $params = ['matricula' => $matriculaId, 'usuario' => Auth::id()];
                $this->_boletimServices[$matriculaId] = new Avaliacao_Service_Boletim($params);
            } catch (Exception $e) {
                $this->messenger->append("Erro ao instanciar serviço boletim para matricula {$matriculaId}: " . $e->getMessage(), 'error', true);
            }
        }

        // validates service
        if (is_null($this->_boletimServices[$matriculaId])) {
            throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");
        }

        return $this->_boletimServices[$matriculaId];
    }

    protected function getNota($etapa, $componenteCurricularId)
    {
        $notaComponente = $this->boletimService()->getNotaComponente($componenteCurricularId, $etapa);

        if (empty($notaComponente)) {
            return '';
        }

        // FIXME #parameters
        $nota = urldecode($this->boletimService()->getNotaComponente($componenteCurricularId, $etapa)->nota);

        return str_replace(',', '.', $nota);
    }

    protected function getEtapaParecer($etapaDefault)
    {
        // FIXME #parameters
        if ($etapaDefault != 'An' && ($this->boletimService()->getRegra()->get('parecerDescritivo') ==
                RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE ||
                // FIXME #parameters
                $this->boletimService()->getRegra()->get('parecerDescritivo') ==
                RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)) {
            $etapaDefault = 'An';
        }

        return $etapaDefault;
    }

    protected function getParecerDescritivo($etapa, $componenteCurricularId)
    {
        // FIXME #parameters
        if ($this->boletimService()->getRegra()->get('parecerDescritivo') ==
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE ||
            // FIXME #parameters
            $this->boletimService()->getRegra()->get('parecerDescritivo') ==
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
            // FIXME #parameters
            return $this->boletimService()->getParecerDescritivo($this->getEtapaParecer($etapa), $componenteCurricularId);
        } else {
            return $this->boletimService()->getParecerDescritivo($this->getEtapaParecer($etapa));
        }
    }

    protected function lancarFaltasNaoLancadas($matriculaId)
    {
        $defaultValue = 0;
        // FIXME #parameters
        $regra = $this->boletimService()->getRegra();
        $tpPresenca = $regra->get('tipoPresenca');

        $regraNaoUsaNota = $this->regraNaoUsaNota($regra->get('tipoNota'));

        $registration = LegacyRegistration::query()->find($matriculaId);
        $componentesCurriculares = $this->loadComponentesCurriculares($registration);

        $ano = $this->boletimService()->getOption('matriculaData')['ano'];
        $escolaId = $this->boletimService()->getOption('matriculaData')['ref_ref_cod_escola'];
        $turmaId = $this->boletimService()->getOption('matriculaData')['ref_cod_turma'];

        $stages = LegacySchoolClassStage::query(['sequencial'])
            ->where(['ref_cod_turma' => $turmaId])
            ->where('data_fim', '<', now())
            ->orderBy('sequencial');

        if (!$stages->exists()) {
            $stages = LegacyAcademicYearStage::query(['sequencial'])
                ->where([
                    'ref_ref_cod_escola' => $escolaId,
                    'ref_ano' => $ano,
                ])
                ->where('data_fim', '<', now())
                ->orderBy('sequencial');
        }

        $getStages = [];
        foreach ($stages->get() as $stage) {
            $getStages[] = $stage->sequencial;
        }

        $etapas = array_map(function ($arr) {
            return $arr;
        }, $getStages);

        if ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            // FIXME #parameters

            foreach ($etapas as $etapa) {
                $hasNotaOrParecerInEtapa = false;

                if ($regraNaoUsaNota) {
                    $hasNotaOrParecerInEtapa = true;
                }

                foreach ($componentesCurriculares as $key => $cc) {
                    $nota = $this->getNota($etapa, $key);
                    $parecer = $this->getParecerDescritivo($etapa, $key);

                    if (!$hasNotaOrParecerInEtapa && (trim($nota) != '' || trim($parecer) != '')) {
                        $hasNotaOrParecerInEtapa = true;
                        break;
                    }
                }

                if ($hasNotaOrParecerInEtapa) {
                    // FIXME #parameters
                    $falta = $this->boletimService()->getFalta($etapa)?->quantidade;

                    if (is_null($falta)) {
                        $notaFalta = new Avaliacao_Model_FaltaGeral([
                            'quantidade' => $defaultValue,
                            'etapa' => $etapa,
                        ]);
                        // FIXME #parameters
                        $this->boletimService()->addFalta($notaFalta);
                        $this->messenger->append("Lançado falta geral (valor $defaultValue) para etapa $etapa (matrícula $matriculaId)", 'notice');
                    }
                }
            }//for etapa
        } elseif ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            // FIXME #parameters
            foreach ($etapas as $etapa) {
                foreach ($componentesCurriculares as $key => $cc) {
                    $nota = $this->getNota($etapa, $key);
                    $parecer = $this->getParecerDescritivo($etapa, $key);

                    if ($regraNaoUsaNota || trim($nota) != '' || trim($parecer) != '') {
                        // FIXME #parameters
                        $falta = $this->boletimService()->getFalta($etapa, $key)?->quantidade;

                        if (is_null($falta)) {
                            // FIXME #parameters
                            $this->boletimService()->addFalta(
                                new Avaliacao_Model_FaltaComponente([
                                    'componenteCurricular' => $key,
                                    'quantidade' => $defaultValue,
                                    'etapa' => $etapa])
                            );

                            $this->messenger->append(Portabilis_String_Utils::toUtf8("Lançado falta (valor $defaultValue) para etapa $etapa e componente curricular {$key} - {$cc} (matricula $matriculaId)"), 'notice');
                        }
                    }
                }
            }
        } else {
            throw new Exception('Tipo de presença desconhecido método lancarFaltasNaoLancadas');
        }
    }

    protected function matriculaId()
    {
        return isset($this->_matriculaId) ? $this->_matriculaId : $this->getRequest()->matricula_id;
    }

    protected function setMatriculaId($id)
    {
        $this->_matriculaId = $id;
    }

    // api responders

    protected function getQuantidadeMatriculas()
    {
        if ($this->canGetQuantidadeMatriculas()) {
            $escolaId = empty($this->getRequest()->escola) ? 0 : $this->getRequest()->escola;
            $cursoId = empty($this->getRequest()->curso) ? 0 : $this->getRequest()->curso;
            $serieId = empty($this->getRequest()->serie) ? 0 : $this->getRequest()->serie;
            $turmaId = empty($this->getRequest()->turma) ? 0 : $this->getRequest()->turma;
            $situacaoMatricula = empty($this->getRequest()->matricula) ? 10 : $this->getRequest()->situacaoMatricula;
            $regraDeAvaliacao = empty($this->getRequest()->regras_avaliacao_id) ? 0 : $this->getRequest()->regras_avaliacao_id;

            $sql = 'SELECT count(m.cod_matricula)
                    FROM pmieducar.matricula AS m
                    INNER JOIN pmieducar.aluno ON aluno.cod_aluno = m.ref_cod_aluno
                    INNER JOIN pmieducar.matricula_turma AS mt ON mt.ref_cod_matricula = m.cod_matricula
                    INNER JOIN pmieducar.serie as s on m.ref_ref_cod_serie = s.cod_serie
                    INNER JOIN modules.regra_avaliacao_serie_ano as ra on ra.serie_id = s.cod_serie and ra.ano_letivo = m.ano
                    WHERE m.ano = $1
                    AND m.ativo = 1
                    AND mt.ref_cod_matricula = m.cod_matricula
                    AND mt.ativo = 1
                    AND (CASE WHEN $2 = 0  THEN TRUE ELSE $2 = m.ref_ref_cod_escola END)
                    AND (CASE WHEN $3 = 0  THEN TRUE ELSE $3 = m.ref_cod_curso END)
                    AND (CASE WHEN $4 = 0  THEN TRUE ELSE $4 = m.ref_ref_cod_serie END)
                    AND (CASE WHEN $5 = 0  THEN TRUE ELSE $5 = mt.ref_cod_turma END)
                    AND (CASE WHEN $6 = 10 THEN TRUE WHEN $6 = 9  THEN m.aprovado NOT IN (4,6) ELSE $6 = m.aprovado END)
                    AND (CASE WHEN $7 = 0  THEN TRUE ELSE $7 = ra.regra_avaliacao_id END)';

            $options = ['params' => [$this->getRequest()->ano, $escolaId, $cursoId, $serieId, $turmaId, $situacaoMatricula, $regraDeAvaliacao], 'return_only' => 'first-field'];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        }
    }

    protected function postPromocaoMatricula()
    {
        if ($this->canPostPromocaoMatricula()) {
            $proximoMatriculaId = $this->loadNextMatriculaId($this->matriculaId());
            $situacaoAnterior = '';
            $novaSituacao = '';

            if ($this->matriculaId() == 0) {
                if (is_numeric($proximoMatriculaId)) {
                    $this->setMatriculaId($proximoMatriculaId);
                    $proximoMatriculaId = $this->loadNextMatriculaId($this->matriculaId());
                } else {
                    $this->messenger->append('Sem matrículas em andamento para a seleção informada.', 'notice');
                }
            }

            if ($this->matriculaId() != 0 && is_numeric($this->matriculaId())) {
                $registration = LegacyRegistration::find($this->matriculaId());
                $_GET['etapa'] = $this->maiorEtapaUtilizada($registration);

                $situacaoAnterior = $this->loadSituacaoArmazenadaMatricula($this->matriculaId());

                $this->lancarFaltasNaoLancadas($this->matriculaId());
                $this->atualizaNotaExame($this->matriculaId());

                $this->trySaveBoletimService();
                $novaSituacao = $this->loadSituacaoArmazenadaMatricula($this->matriculaId());

                if ($situacaoAnterior != $novaSituacao) {
                    if ($novaSituacao == 1) {
                        $this->messenger->append("Matrícula {$this->matriculaId()} foi aprovada (situaçao antiga $situacaoAnterior)", 'success');
                    } elseif ($novaSituacao == 2) {
                        $this->messenger->append("Matrícula {$this->matriculaId()} foi reprovada (situaçao antiga $situacaoAnterior)", 'success');
                    } else {
                        $this->messenger->append("Matrícula {$this->matriculaId()} teve a situação alterada de $novaSituacao para $situacaoAnterior)", 'notice');
                    }
                }
            }

            return [
                'proximo_matricula_id' => $proximoMatriculaId,
                'situacao_anterior' => $situacaoAnterior,
                'nova_situacao' => $novaSituacao,
            ];
        }
    }

    protected function atualizaNotaExame($matriculaId): void
    {
        foreach (App_Model_IedFinder::getComponentesPorMatricula($matriculaId) as $_componente) {
            $componenteId = $_componente->get('id');
            $nota_exame = str_replace(',', '.', $this->boletimService()->preverNotaRecuperacao($componenteId));

            if (!empty($nota_exame)) {
                $this->createOrUpdateNotaExame($matriculaId, $componenteId, $nota_exame);

                return;
            }

            $this->deleteNotaExame($matriculaId, $componenteId);
        }
    }

    protected function createOrUpdateNotaExame($matriculaId, $componenteCurricularId, $notaExame)
    {
        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId, $notaExame);

        return $obj->existe() ? $obj->edita() : $obj->cadastra();
    }

    protected function deleteNotaExame($matriculaId, $componenteCurricularId)
    {
        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId);

        return $obj->excluir();
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'quantidade_matriculas')) {
            $this->appendResponse('quantidade_matriculas', $this->getQuantidadeMatriculas());
            $this->appendResponse('ano', (int) $this->getRequest()->ano);
            $this->appendResponse('instituicao', $this->getInstitutionName($this->getRequest()->instituicao_id));
            $this->appendResponse('escola', $this->getSchoolName($this->getRequest()->escola));
            $this->appendResponse('curso', $this->getCourseName($this->getRequest()->curso));
            $this->appendResponse('serie', $this->getGradeName($this->getRequest()->serie));
            $this->appendResponse('turma', $this->getSchoolClassName($this->getRequest()->turma));
            $this->appendResponse('matricula', $this->getStudentName($this->getRequest()->matricula));
            $this->appendResponse('situacaoMatricula', $this->getRegistrationStatus($this->getRequest()->situacaoMatricula));
            $this->appendResponse('regraAvaliacao', $this->getEvaluationRuleName($this->getRequest()->regras_avaliacao_id));
        } elseif ($this->isRequestFor('post', 'promocao')) {
            $this->appendResponse('result', $this->postPromocaoMatricula());
        } else {
            $this->notImplementedOperationError();
        }
    }

    private function getInstitutionName($institutionId)
    {
        return LegacyInstitution::query()->find($institutionId)?->nm_instituicao;
    }

    private function getSchoolName($schoolId)
    {
        if (empty($schoolId)) {
            return ' - ';
        }

        return LegacySchool::query()->find($schoolId)?->name;
    }

    /**
     * Verifica se a regra de avaliação não usa nota
     *
     * @param int $tipoNota
     * @return bool
     */
    private function regraNaoUsaNota($tipoNota)
    {
        return $tipoNota == RegraAvaliacao_Model_Nota_TipoValor::NENHUM;
    }

    private function maiorEtapaUtilizada($registration)
    {
        $where = [
            'ref_ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_ano' => $registration->ano,
        ];

        return LegacyAcademicYearStage::query()->where($where)->count();
    }

    public function processEnrollmentsPromotion(int $userId, int $enrollmentsId, bool $updateScore = false): void
    {
        $registration = LegacyRegistration::query()->find($enrollmentsId);

        if (empty($registration)) {
            return;
        }

        $this->setMatriculaId($enrollmentsId);
        $maiorEtapaUtilizada = $this->maiorEtapaUtilizada($registration);

        $params = [
            'matricula' => $enrollmentsId,
            'user_id' => $userId,
            'etapa' => $maiorEtapaUtilizada,
            'updateScore' => $updateScore,
        ];

        $this->boletimService(
            build: true,
            params: $params
        );

        $this->lancarFaltasNaoLancadas($enrollmentsId);
        $this->atualizaNotaExame($enrollmentsId);
        $this->trySaveBoletimService();
    }

    private function getCourseName(mixed $curso)
    {
        if (empty($curso)) {
            return ' - ';
        }

        return LegacyCourse::query()->find($curso)?->nm_curso;
    }

    private function getGradeName(mixed $serie)
    {
        if (empty($serie)) {
            return ' - ';
        }

        return LegacyGrade::query()->find($serie)?->nm_serie;
    }

    private function getSchoolClassName(mixed $schoolClassId)
    {
        if (empty($schoolClassId)) {
            return ' - ';
        }

        return LegacySchoolClass::query()->find($schoolClassId)?->nm_turma;
    }

    private function getStudentName(mixed $registrationId)
    {
        if (empty($registrationId)) {
            return ' - ';
        }

        return LegacyRegistration::query()->find($registrationId)?->student->name;
    }

    private function getRegistrationStatus(mixed $registrationStatusId)
    {
        if (empty($registrationStatusId)) {
            return ' - ';
        }

        return EnrollmentStatusFilter::getDescriptiveValues()[$registrationStatusId];
    }

    private function getEvaluationRuleName(mixed $evaluationRuleId)
    {
        if (empty($evaluationRuleId)) {
            return 'Todas';
        }

        return LegacyEvaluationRule::query()->find($evaluationRuleId)?->name;
    }
}
