<?php

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacySchoolStage;

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
                INNER JOIN pmieducar.matricula_turma AS mt ON m.cod_matricula = mt.ref_cod_matricula
                INNER JOIN pmieducar.serie as s on m.ref_ref_cod_serie = s.cod_serie
                INNER JOIN modules.regra_avaliacao_serie_ano as ra on ra.serie_id = s.cod_serie and ra.ano_letivo = m.ano
             WHERE m.ano = $1
               AND m.ativo = 1
               AND mt.ref_cod_matricula = m.cod_matricula
               AND mt.ativo = 1
               AND ref_cod_matricula > $2
               AND (CASE WHEN $3 = 0  THEN true else $3 = m.ref_ref_cod_escola END)
               AND (CASE WHEN $4 = 0  THEN true else $4 = m.ref_cod_curso END)
               AND (CASE WHEN $5 = 0  THEN true else $5 = m.ref_ref_cod_serie END)
               AND (CASE WHEN $6 = 0  THEN true else $6 = mt.ref_cod_turma END)
               AND (CASE WHEN $7 = 10 THEN true
                         WHEN $7 = 9  THEN m.aprovado NOT IN (4,6) ELSE $6 = m.aprovado END)
                AND (CASE WHEN $8 = 0  THEN true ELSE $8 = ra.regra_avaliacao_id END)
          ORDER BY ref_cod_matricula
             LIMIT 1';

        $options = ['params' => [$this->getRequest()->ano, $currentMatriculaId, $escolaId, $cursoId, $serieId, $turmaId, $matricula,$regraDeAvaliacao],
            'return_only' => 'first-field'];

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

    protected function loadDadosMatricula($matriculaId)
    {
        $sql = 'SELECT m.cod_matricula AS matricula_id,
                   m.ref_cod_aluno AS aluno_id,
                   m.ref_ref_cod_escola AS escola_id,
                   m.ref_cod_curso AS curso_id,
                   m.ref_ref_cod_serie AS serie_id,
                   mt.ref_cod_turma AS turma_id,
                   m.ano,
                   m.aprovado
              FROM pmieducar.matricula  AS m
        INNER JOIN pmieducar.matricula_turma AS mt ON mt.ref_cod_matricula = m.cod_matricula
             WHERE mt.ativo = 1
               AND cod_matricula = $1
             LIMIT 1';

        $options = ['params' => $matriculaId, 'return_only' => 'first-row'];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
    }

    #TODO substituir este metodo por service->getComponentes()?
    protected function loadComponentesCurriculares($matriculaId)
    {
        $dadosMatricula = $this->loadDadosMatricula($matriculaId);

        $anoEscolar = $dadosMatricula['ano'];
        $escolaId = $dadosMatricula['escola_id'];
        $turmaId = $dadosMatricula['turma_id'];

        $sql = 'SELECT cc.id, cc.nome
              FROM modules.componente_curricular_turma AS cct
        INNER JOIN modules.componente_curricular AS cc ON cct.componente_curricular_id = cc.id
        INNER JOIN pmieducar.escola_ano_letivo AS al ON cct.escola_id = al.ref_cod_escola
             WHERE cct.turma_id = $1
               AND cct.escola_id = $2
               AND al.ano = $3
               AND cc.instituicao_id = $4';

        $options = ['params' => [$turmaId, $escolaId, $anoEscolar, $this->getRequest()->instituicao_id]];
        $componentesCurricularesTurma = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

        if (count($componentesCurricularesTurma)) {
            return $componentesCurricularesTurma;
        }

        $sql = 'SELECT cc.id, cc.nome
              FROM pmieducar.turma AS t
        INNER JOIN pmieducar.escola_serie_disciplina AS esd ON t.ref_ref_cod_serie = esd.ref_ref_cod_serie
        INNER JOIN modules.componente_curricular AS cc ON esd.ref_cod_disciplina = cc.id
        INNER JOIN pmieducar.escola_ano_letivo AS al ON esd.ref_ref_cod_escola = al.ref_cod_escola
             WHERE t.cod_turma = $1
               AND esd.ref_ref_cod_escola = $2
               AND al.ano = $3
               AND cc.instituicao_id = $4
               AND t.ativo = 1
               AND esd.ativo = 1
               AND al.ativo = 1';

        $options = ['params' => [$turmaId, $escolaId, $anoEscolar, $this->getRequest()->instituicao_id]];
        $componentesCurricularesSerie = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

        return $componentesCurricularesSerie;
    }

    protected function trySaveBoletimService()
    {
        try {
            // FIXME #parameters
            $this->boletimService()->save();
        } catch (CoreExt_Service_Exception $e) {
            // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
            // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
        }
    }

    protected function boletimService($reload = false)
    {
        $matriculaId = $this->matriculaId();

        if (!isset($this->_boletimServices)) {
            $this->_boletimServices = [];
        }

        if (!isset($this->_boletimServices[$matriculaId]) || $reload) {
            // set service
            try {
                $params = ['matricula' => $matriculaId, 'usuario' => \Illuminate\Support\Facades\Auth::id()];
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

        $componentesCurriculares = $this->loadComponentesCurriculares($matriculaId);

        $ano = $this->boletimService()->getOption('matriculaData')['ano'];
        $escolaId = $this->boletimService()->getOption('matriculaData')['ref_ref_cod_escola'];
        $turmaId = $this->boletimService()->getOption('matriculaData')['ref_cod_turma'];

        $stages = LegacySchoolClassStage::query(['sequencial'])
            ->where(['ref_cod_turma' => $turmaId])
            ->where('data_fim', '<', now())
            ->orderBy('sequencial');

        if (!$stages->exists()) {
            $stages = LegacySchoolStage::query(['sequencial'])
                ->where([
                    'ref_ref_cod_escola' => $escolaId,
                    'ref_ano' => $ano
                ])
                ->where('data_fim', '<', now())
                ->orderBy('sequencial');
        }

        foreach ($stages->get() as $stage) {
            $getStages[] = $stage->sequencial;
        }

        $etapas = array_map(function ($arr) {
            return $arr;
        }, $getStages);

        if ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            // FIXME #parameters

            foreach ($etapas as $etapa) {
                // FIXME #parameters
                $falta = $this->boletimService()->getFalta($etapa) ? $this->boletimService()->getFalta($etapa)->quantidade : null;

                if (is_null($falta)) {
                    $notaFalta = new Avaliacao_Model_FaltaGeral([
                        'quantidade' => $defaultValue,
                        'etapa' => $etapa
                    ]);
                    // FIXME #parameters
                    $this->boletimService()->addFalta($notaFalta);
                    $this->messenger->append("Lançado falta geral (valor $defaultValue) para etapa $etapa (matrícula $matriculaId)", 'notice');
                }
            }//for etapa
        } elseif ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            // FIXME #parameters
            foreach ($etapas as $etapa) {
                foreach ($componentesCurriculares as $cc) {
                    // FIXME #parameters
                    $falta = $this->boletimService()->getFalta($etapa, $cc['id'])->quantidade;

                    if (is_null($falta)) {
                        // FIXME #parameters
                        $this->boletimService()->addFalta(
                            new Avaliacao_Model_FaltaComponente([
                                'componenteCurricular' => $cc['id'],
                                'quantidade' => $defaultValue,
                                'etapa' => $etapa])
                        );

                        $this->messenger->append("Lançado falta (valor $defaultValue) para etapa $etapa e componente curricular {$cc['id']} - {$cc['nome']} (matricula $matriculaId)", 'notice');
                    }
                }
            }
        } else {
            throw new Exception('Tipo de presença desconhecido método lancarFaltasNaoLancadas');
        }
    }

    protected function matriculaId()
    {
        return (isset($this->_matriculaId) ? $this->_matriculaId : $this->getRequest()->matricula_id);
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
            $matricula = empty($this->getRequest()->matricula) ? 10 : $this->getRequest()->matricula;
            $regraDeAvaliacao = empty($this->getRequest()->regras_avaliacao_id) ? 0 : $this->getRequest()->regras_avaliacao_id;

            $sql = 'SELECT count(m.cod_matricula)
                    FROM pmieducar.matricula AS m
                    INNER JOIN pmieducar.matricula_turma AS mt ON mt.ref_cod_matricula = m.cod_matricula
                    INNER JOIN pmieducar.serie as s on m.ref_ref_cod_serie = s.cod_serie
                    INNER JOIN modules.regra_avaliacao_serie_ano as ra on ra.serie_id = s.cod_serie and ra.ano_letivo = m.ano
                    WHERE m.ano = $1
                      AND m.ativo = 1
                      AND mt.ref_cod_matricula = m.cod_matricula
                      AND mt.ativo = 1
                      AND (CASE WHEN $2 = 0  THEN true ELSE $2 = m.ref_ref_cod_escola END)
                      AND (CASE WHEN $3 = 0  THEN true ELSE $3 = m.ref_cod_curso END)
                      AND (CASE WHEN $4 = 0  THEN true ELSE $4 = m.ref_ref_cod_serie END)
                      AND (CASE WHEN $5 = 0  THEN true ELSE $5 = mt.ref_cod_turma END)
                      AND (CASE WHEN $6 = 10 THEN true WHEN $6 = 9  THEN m.aprovado NOT IN (4,6) ELSE $6 = m.aprovado END)
                      AND (CASE WHEN $7 = 0  THEN true ELSE $7 = ra.regra_avaliacao_id END)';


            $options = ['params' => [$this->getRequest()->ano, $escolaId, $cursoId, $serieId, $turmaId, $matricula, $regraDeAvaliacao], 'return_only' => 'first-field'];

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
                //$this->convertParecerToLatin1($matriculaId);
                $this->atualizaNotaExame();

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

            return ['proximo_matricula_id' => $proximoMatriculaId,
                'situacao_anterior' => $situacaoAnterior,
                'nova_situacao' => $novaSituacao];
        }
    }

    /* remove notas, medias notas e faltas lnçadas para componentes curriculares não mais vinculados
      as das turmas / séries para que os alunos destas possam ser promovidos */
    protected function deleteOldComponentesCurriculares()
    {
        if ($this->canDeleteOldComponentesCurriculares()) {
            CleanComponentesCurriculares::destroyOldResources($this->getRequest()->ano);

            $this->messenger->append('Removido notas, medias notas e faltas de antigos componentes curriculares, ' .
                'vinculados a turmas / séries.', 'notice');
        }
    }

    protected function atualizaNotaExame()
    {
        $matriculaId = $this->matriculaId();

        foreach (App_Model_IedFinder::getComponentesPorMatricula($matriculaId) as $_componente) {
            $componenteId = $_componente->get('id');

            // FIXME #parameters
            $nota_exame = str_replace(',', '.', $this->boletimService()->preverNotaRecuperacao($componenteId));

            if (!empty($nota_exame)) {
                $this->createOrUpdateNotaExame($matriculaId, $componenteId, $nota_exame);
            } else {
                $this->deleteNotaExame($matriculaId, $componenteId);
            }
        }
    }

    protected function createOrUpdateNotaExame($matriculaId, $componenteCurricularId, $notaExame)
    {
        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId, $notaExame);

        return ($obj->existe() ? $obj->edita() : $obj->cadastra());
    }

    protected function deleteNotaExame($matriculaId, $componenteCurricularId)
    {
        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId);

        return ($obj->excluir());
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'quantidade_matriculas')) {
            $this->appendResponse('quantidade_matriculas', $this->getQuantidadeMatriculas());
        } elseif ($this->isRequestFor('post', 'promocao')) {
            $this->appendResponse('result', $this->postPromocaoMatricula());
        } elseif ($this->isRequestFor('delete', 'old_componentes_curriculares')) {
            $this->appendResponse('result', $this->deleteOldComponentesCurriculares());
        } else {
            $this->notImplementedOperationError();
        }
    }

    /**
     * Verifica se a regra de avaliação não usa nota
     *
     * @param int $tipoNota
     *
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

        return LegacySchoolStage::query()->where($where)->count();
    }
}
