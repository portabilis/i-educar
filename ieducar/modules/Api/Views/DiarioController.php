<?php

use App\Services\RemoveHtmlTagsStringService;
use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotAllowGeneralAbsence;
use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use iEducar\Support\Exceptions\Error;

class DiarioController extends ApiCoreController
{
    protected $_processoAp = 642;

    protected function getRegra($matriculaId)
    {
        return App_Model_IedFinder::getRegraAvaliacaoPorMatricula($matriculaId);
    }

    protected function getComponentesPorMatricula($matriculaId)
    {
        return App_Model_IedFinder::getComponentesPorMatricula($matriculaId);
    }

    protected function getComponentesPorTurma($turmaId,$matriculaId = null)
    {
        $objTurma = new clsPmieducarTurma($turmaId);
        $detTurma = $objTurma->detalhe();
        $escolaId = $detTurma['ref_ref_cod_escola'];
        $serieId = $detTurma['ref_ref_cod_serie'];
        $ano = $detTurma['ano'];

        //obtem a série da matrícula
        if ($matriculaId && $detTurma['multiseriada'] == 1) {
            $serieId = $this->getSeriePorMatricula($matriculaId) ?: $serieId;
        }

        return App_Model_IedFinder::getComponentesTurma($serieId, $escolaId, $turmaId, null, null, null, null, null, $ano);
    }

    private function getSeriePorMatricula($matriculaId) {
        return \App\Models\LegacyRegistration::where('cod_matricula',$matriculaId)->value('ref_ref_cod_serie');
    }

    protected function validateComponenteCurricular($matriculaId, $componenteCurricularId)
    {
        $componentes = $this->getComponentesPorMatricula($matriculaId);
        $componentes = CoreExt_Entity::entityFilterAttr($componentes, 'id', 'id');
        $valid = in_array($componenteCurricularId, $componentes);

        if (!$valid) {
            throw new CoreExt_Exception("Componente curricular de código {$componenteCurricularId} não existe para essa turma/matrícula.");
        }

        return $valid;
    }

    protected function validateComponenteTurma($turmaId, $componenteCurricularId,$matriculaId = null)
    {
        $componentesTurma = $this->getComponentesPorTurma($turmaId,$matriculaId);
        if ($componentesTurma instanceof CoreExt_Entity) {
            $componentesTurma = CoreExt_Entity::entityFilterAttr($componentesTurma, 'id', 'id');
        } else {
            foreach ($componentesTurma as $componente) {
                $arr[] = $componente->id;
                $key = key($arr);
                $componentes[$key] = $arr;
            }
            $componentesTurma = $componentes[0];
        }

        $valid = in_array($componenteCurricularId, $componentesTurma);
        if (!$valid) {
            $this->messenger->append("Componente curricular de código {$componenteCurricularId} não existe para a turma {$turmaId}.", 'error');
            $this->appendResponse('error', [
                'code' => Error::DISCIPLINE_NOT_EXISTS_FOR_SCHOOL_CLASS,
                'message' => "Componente curricular de código {$componenteCurricularId} não existe para a turma {$turmaId}.",
            ]);

            return false;
        }

        return $valid;
    }

    protected function trySaveServiceBoletim($turmaId, $alunoId)
    {
        try {
            $this->serviceBoletim($turmaId, $alunoId)->save();
        } catch (CoreExt_Service_Exception) {
            // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
            // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
        }
    }

    protected function trySaveServiceBoletimFaltas($turmaId, $alunoId)
    {
        try {
            $this->serviceBoletim($turmaId, $alunoId)->saveFaltas();
            $this->serviceBoletim($turmaId, $alunoId)->promover();
        } catch (CoreExt_Service_Exception) {
            //...
        }
    }

    protected function findMatriculaByTurmaAndAluno($turmaId, $alunoId)
    {
        $resultado = [];

        $sql = 'SELECT m.cod_matricula AS id
              FROM pmieducar.matricula m
              INNER JOIN pmieducar.matricula_turma mt ON m.cod_matricula = mt.ref_cod_matricula
              WHERE m.ativo = 1
              AND (mt.ativo = 1
                   OR mt.transferido
                  )
              AND mt.ref_cod_turma = $1
              AND m.ref_cod_aluno = $2
              AND m.aprovado IN (1,2,3,4,13,12,14)
            ORDER BY m.aprovado
              LIMIT 1';

        $matriculaId = $this->fetchPreparedQuery($sql, [$turmaId, $alunoId], true, 'first-field');

        return $matriculaId;
    }

    /**
     * @param int $turmaId
     * @param int $alunoId
     *
     * @return Avaliacao_Service_Boletim|bool
     *
     * @throws CoreExt_Exception
     */
    protected function serviceBoletim($turmaId, $alunoId)
    {
        $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

        if ($matriculaId) {
            if (!isset($this->_boletimServiceInstances)) {
                $this->_boletimServiceInstances = [];
            }

            // set service
            if (!isset($this->_boletimServiceInstances[$matriculaId])) {
                $params = ['matricula' => $matriculaId];
                $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
            }

            // validates service
            if (is_null($this->_boletimServiceInstances[$matriculaId])) {
                throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matrícula {$matriculaId}.");
            }

            return $this->_boletimServiceInstances[$matriculaId];
        } else {
            return false;
        }
    }

    protected function canPostNotas()
    {
        return $this->validatesPresenceOf('notas') && $this->validatesPresenceOf('etapa');
    }

    protected function canPostFaltasPorComponente()
    {
        return $this->validatesPresenceOf('faltas') && $this->validatesPresenceOf('etapa');
    }

    protected function canPostFaltasGeral()
    {
        return $this->validatesPresenceOf('faltas') && $this->validatesPresenceOf('etapa');
    }

    protected function canPostPareceresPorEtapaComponente()
    {
        return $this->validatesPresenceOf('pareceres') && $this->validatesPresenceOf('etapa');
    }

    protected function canPostPareceresAnualPorComponente()
    {
        return $this->validatesPresenceOf('pareceres');
    }

    protected function canPostPareceresAnualGeral()
    {
        return $this->validatesPresenceOf('pareceres');
    }

    protected function canPostPareceresPorEtapaGeral()
    {
        return $this->validatesPresenceOf('pareceres') && $this->validatesPresenceOf('etapa');
    }

    /**
     * @return bool
     *
     * @throws CoreExt_Exception
     * @throws MissingStagesException
     */
    protected function postNotas()
    {
        if (! $this->canPostNotas()) {
            return false;
        }

        $etapa = $this->getRequest()->etapa;
        $notas = $this->getRequest()->notas;

        foreach ($notas as $turmaId => $notaTurma) {
            foreach ($notaTurma as $alunoId => $notaTurmaAluno) {
                $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                if (empty($matriculaId)) {
                    continue;
                }

                foreach ($notaTurmaAluno as $componenteCurricularId => $notaTurmaAlunoDisciplina) {
                    if (!$this->validateComponenteTurma($turmaId, $componenteCurricularId,$matriculaId)) {
                        continue;
                    }

                    if (! $serviceBoletim = $this->serviceBoletim($turmaId, $alunoId)) {
                        continue;
                    }

                    $regra = $serviceBoletim->getEvaluationRule();

                    $notaOriginal = $notaTurmaAlunoDisciplina['nota'];
                    $notaRecuperacao = $notaTurmaAlunoDisciplina['recuperacao'];
                    $nomeCampoRecuperacao = $this->defineCampoTipoRecuperacao($matriculaId);
                    $notaOriginal = $this->truncate($notaOriginal, 4);

                    $valorNota = $serviceBoletim->calculateStageScore($etapa, $notaOriginal, $notaRecuperacao);

                    $notaValidacao = $notaOriginal;
                    if (is_numeric($valorNota)) {
                        $notaValidacao = $valorNota;
                    }

                    if ($etapa == 'Rc' && $notaValidacao > $regra->nota_maxima_exame_final) {
                        $this->messenger->append("A nota {$valorNota} está acima da configurada para nota máxima para exame que é {$regra->nota_maxima_exame_final}.", 'error');
                        $this->appendResponse('error', [
                            'code' => Error::EXAM_SCORE_GREATER_THAN_MAX_ALLOWED,
                            'message' => "A nota {$valorNota} está acima da configurada para nota máxima para exame que é {$regra->nota_maxima_exame_final}.",
                        ]);

                        return false;
                    }

                    if ($etapa != 'Rc' && $notaValidacao > $regra->nota_maxima_geral && !$regra->isSumScoreCalculation()) {
                        $this->messenger->append("A nota {$valorNota} está acima da configurada para nota máxima geral que é {$regra->nota_maxima_geral}.", 'error');
                        $this->appendResponse('error', [
                            'code' => Error::SCORE_GREATER_THAN_MAX_ALLOWED,
                            'message' => "A nota {$valorNota} está acima da configurada para nota máxima geral que é {$regra->nota_maxima_geral}.",
                        ]);

                        return false;
                    }

                    if ($notaValidacao < $regra->nota_minima_geral) {
                        $this->messenger->append("A nota {$valorNota} está abaixo da configurada para nota mínima geral que é {$regra->nota_minima_geral}.", 'error');
                        $this->appendResponse('error', [
                            'code' => Error::SCORE_LESSER_THAN_MIN_ALLOWED,
                            'message' => "A nota {$valorNota} está abaixo da configurada para nota mínima geral que é {$regra->nota_minima_geral}.",
                        ]);

                        return false;
                    }

                    $array_nota = [
                        'componenteCurricular' => $componenteCurricularId,
                        'nota' => $valorNota,
                        'etapa' => $etapa,
                        'notaOriginal' => $notaOriginal
                    ];

                    if (!empty($nomeCampoRecuperacao)) {
                        $array_nota[$nomeCampoRecuperacao] = $notaRecuperacao;
                    }

                    $nota = new Avaliacao_Model_NotaComponente($array_nota);

                    $serviceBoletim->verificaNotasLancadasNasEtapasAnteriores(
                        $etapa,
                        $componenteCurricularId
                    );

                    $serviceBoletim->addNota($nota);

                    $this->trySaveServiceBoletim($turmaId, $alunoId);

                    $this->atualizaNotaNecessariaExame($turmaId, $alunoId, $componenteCurricularId);
                }
            }

            $this->messenger->append('Notas postadas com sucesso!', 'success');
        }

    }

    protected function postRecuperacoes()
    {
        if ($this->canPostNotas()) {
            $etapa = $this->getRequest()->etapa;
            $notas = $this->getRequest()->notas;

            foreach ($notas as $turmaId => $notaTurma) {
                foreach ($notaTurma as $alunoId => $notaTurmaAluno) {
                    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                    if (empty($matriculaId)) {
                        continue;
                    }

                    foreach ($notaTurmaAluno as $componenteCurricularId => $notaTurmaAlunoDisciplina) {
                        if ($this->validateComponenteTurma($turmaId, $componenteCurricularId,$matriculaId)) {
                            $notaOriginal = $notaTurmaAlunoDisciplina['nota'];

                            if (is_null($notaOriginal)) {
                                $notaOriginalPersistida = $this->serviceBoletim($turmaId, $alunoId)->getNotaComponente($componenteCurricularId, $etapa)->notaOriginal;

                                if (is_null($notaOriginalPersistida)) {
                                    $notaOriginal = 0.0;
                                } else {
                                    $notaOriginal = $notaOriginalPersistida;
                                }
                            }

                            if (! $serviceBoletim = $this->serviceBoletim($turmaId, $alunoId)) {
                                continue;
                            }

                            $regra = $serviceBoletim->getRegra();

                            $notaRecuperacao = $notaTurmaAlunoDisciplina['recuperacao'];
                            $nomeCampoRecuperacao = $this->defineCampoTipoRecuperacao($matriculaId);

                            $valorNota = $serviceBoletim->calculateStageScore($etapa, $notaOriginal, $notaRecuperacao);
                            $notaMaximaPermitida = $regra->getNotaMaximaRecuperacao($etapa);

                            if (empty($notaMaximaPermitida)) {
                                $this->messenger->append('A nota máxima para recuperação não foi definida', 'error');

                                return false;
                            }

                            if ($notaRecuperacao > $notaMaximaPermitida) {
                                $this->messenger->append("A nota {$valorNota} está acima da configurada para nota máxima para exame que é {$notaMaximaPermitida}.", 'error');

                                return false;
                            }

                            $notaOriginal = $this->truncate($notaOriginal, 4);
                            $array_nota = [
                                'componenteCurricular' => $componenteCurricularId,
                                'nota' => $valorNota,
                                'etapa' => $etapa,
                                'notaOriginal' => $notaOriginal,
                                $nomeCampoRecuperacao => $notaRecuperacao
                            ];

                            $nota = new Avaliacao_Model_NotaComponente($array_nota);

                            if ($this->serviceBoletim($turmaId, $alunoId)) {
                                $this->serviceBoletim($turmaId, $alunoId)->addNota($nota);
                                $this->trySaveServiceBoletim($turmaId, $alunoId);
                            }
                        }
                    }
                }

                $this->messenger->append('Recuperacoes postadas com sucesso!', 'success');
            }
        }
    }

    private function defineCampoTipoRecuperacao($matriculaId)
    {
        $regra = $this->getRegra($matriculaId);

        return match ((int)$regra->get('tipoRecuperacaoParalela')) {
            RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA => 'notaRecuperacaoParalela',
            RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS => 'notaRecuperacaoEspecifica',
            default => '',
        };
    }

    protected function postFaltasPorComponente()
    {
        if ($this->canPostFaltasPorComponente()) {
            $etapa = $this->getRequest()->etapa;
            $faltas = $this->getRequest()->faltas;

            foreach ($faltas as $turmaId => $faltaTurma) {
                foreach ($faltaTurma as $alunoId => $faltaTurmaAluno) {
                    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                    if (empty($matriculaId)) {
                        continue;
                    }

                    if ($this->getRegra($matriculaId)->get('tipoPresenca') != RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
                        $this->messenger->append("A regra da turma $turmaId não permite lançamento de faltas por componente.", 'error');
                        $this->appendResponse('error', [
                            'code' => Error::SCHOOL_CLASS_DOESNT_ALOW_FREQUENCY_BY_DISCIPLINE,
                            'message' => "A regra da turma $turmaId não permite lançamento de faltas por componente.",
                        ]);

                        return false;
                    }

                    foreach ($faltaTurmaAluno as $componenteCurricularId => $faltaTurmaAlunoDisciplina) {
                        if ($matriculaId) {
                            if ($this->validateMatricula($matriculaId)) {
                                if ($this->validateComponenteTurma($turmaId, $componenteCurricularId,$matriculaId)) {
                                    $valor = $faltaTurmaAlunoDisciplina['valor'];

                                    $falta = new Avaliacao_Model_FaltaComponente([
                                        'componenteCurricular' => $componenteCurricularId,
                                        'quantidade' => $valor,
                                        'etapa' => $etapa,
                                    ]);

                                    $this->serviceBoletim($turmaId, $alunoId)->addFalta($falta);
                                    $this->trySaveServiceBoletimFaltas($turmaId, $alunoId);
                                }
                            }
                        }
                    }
                }
            }

            $this->messenger->append('Faltas postadas com sucesso!', 'success');
        }
    }

    /**
     * @throws CoreExt_Exception
     * @throws EvaluationRuleNotAllowGeneralAbsence
     */
    protected function postFaltasGeral()
    {
        if ($this->canPostFaltasPorComponente()) {
            $etapa = $this->getRequest()->etapa;
            $faltas = $this->getRequest()->faltas;

            foreach ($faltas as $turmaId => $faltaTurma) {
                foreach ($faltaTurma as $alunoId => $faltaTurmaAluno) {
                    $faltas = $faltaTurmaAluno['valor'];
                    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                    if (empty($matriculaId)) {
                        continue;
                    }

                    if ($this->getRegra($matriculaId)->get('tipoPresenca') != RegraAvaliacao_Model_TipoPresenca::GERAL) {
                        throw new EvaluationRuleNotAllowGeneralAbsence($turmaId);
                    }

                    $falta = new Avaliacao_Model_FaltaGeral([
                        'quantidade' => $faltas,
                        'etapa' => $etapa,
                    ]);

                    $this->serviceBoletim($turmaId, $alunoId)->addFalta($falta);
                    $this->trySaveServiceBoletimFaltas($turmaId, $alunoId);
                }
            }

            $this->messenger->append('Faltas postadas com sucesso!', 'success');
        }
    }

    protected function postPareceresPorEtapaComponente()
    {
        if ($this->canPostPareceresPorEtapaComponente()) {
            $pareceres = $this->getRequest()->pareceres;
            $etapa = $this->getRequest()->etapa;

            foreach ($pareceres as $turmaId => $parecerTurma) {
                foreach ($parecerTurma as $alunoId => $parecerTurmaAluno) {
                    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                    if (!empty($matriculaId)) {
                        if ($this->getRegra($matriculaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE) {
                            throw new CoreExt_Exception("A regra da turma {$turmaId} não permite lançamento de pareceres por etapa e componente.");
                        }

                        foreach ($parecerTurmaAluno as $componenteCurricularId => $parecerTurmaAlunoComponente) {
                            if ($this->validateComponenteTurma($turmaId, $componenteCurricularId,$matriculaId)) {

                                $parecerDescritivo = new Avaliacao_Model_ParecerDescritivoComponente([
                                    'componenteCurricular' => $componenteCurricularId,
                                    'parecer' => $this->removeHtmlTags($parecerTurmaAlunoComponente['valor']),
                                    'etapa' => $etapa,
                                ]);

                                $this->serviceBoletim($turmaId, $alunoId)->addParecer($parecerDescritivo);
                                $this->trySaveServiceBoletim($turmaId, $alunoId);
                            }
                        }
                    }
                }
            }

            $this->messenger->append('Pareceres postados com sucesso!', 'success');
        }
    }

    protected function postPareceresAnualPorComponente()
    {
        if ($this->canPostPareceresAnualPorComponente()) {
            $pareceres = $this->getRequest()->pareceres;

            foreach ($pareceres as $turmaId => $parecerTurma) {
                foreach ($parecerTurma as $alunoId => $parecerTurmaAluno) {
                    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                    if (!empty($matriculaId)) {
                        if ($this->getRegra($matriculaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
                            throw new CoreExt_Exception("A regra da turma {$turmaId} não permite lançamento de pareceres anual por componente.");
                        }

                        foreach ($parecerTurmaAluno as $componenteCurricularId => $parecerTurmaAlunoComponente) {
                            if ($this->validateComponenteCurricular($matriculaId, $componenteCurricularId)) {

                                $parecerDescritivo = new Avaliacao_Model_ParecerDescritivoComponente([
                                    'componenteCurricular' => $componenteCurricularId,
                                    'parecer' => $this->removeHtmlTags($parecerTurmaAlunoComponente['valor']),
                                ]);

                                $this->serviceBoletim($turmaId, $alunoId)->addParecer($parecerDescritivo);
                                $this->trySaveServiceBoletim($turmaId, $alunoId);
                            }
                        }
                    }
                }
            }

            $this->messenger->append('Pareceres postados com sucesso!', 'success');
        }
    }

    protected function postPareceresPorEtapaGeral()
    {
        if ($this->canPostPareceresPorEtapaGeral()) {
            $pareceres = $this->getRequest()->pareceres;
            $etapa = $this->getRequest()->etapa;

            foreach ($pareceres as $turmaId => $parecerTurma) {
                foreach ($parecerTurma as $alunoId => $parecerTurmaAluno) {
                    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                    if (!empty($matriculaId)) {
                        if ($this->getRegra($matriculaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL) {
                            throw new CoreExt_Exception("A regra da turma {$turmaId} não permite lançamento de pareceres por etapa geral.");
                        }

                        $parecerDescritivo = new Avaliacao_Model_ParecerDescritivoGeral([
                            'parecer' => $this->removeHtmlTags($parecerTurmaAluno['valor']),
                            'etapa' => $etapa,
                        ]);

                        $this->serviceBoletim($turmaId, $alunoId)->addParecer($parecerDescritivo);
                        $this->trySaveServiceBoletim($turmaId, $alunoId);
                    }
                }
            }

            $this->messenger->append('Pareceres postados com sucesso!', 'success');
        }
    }

    protected function postPareceresAnualGeral()
    {
        if ($this->canPostPareceresAnualGeral()) {
            $pareceres = $this->getRequest()->pareceres;

            foreach ($pareceres as $turmaId => $parecerTurma) {
                foreach ($parecerTurma as $alunoId => $parecerTurmaAluno) {
                    $parecer = $this->removeHtmlTags($parecerTurmaAluno['valor']);
                    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

                    if (!empty($matriculaId)) {
                        if ($this->getRegra($matriculaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL) {
                            throw new CoreExt_Exception("A regra da turma {$turmaId} não permite lançamento de pareceres anual geral.");
                        }

                        $parecerDescritivo = new Avaliacao_Model_ParecerDescritivoGeral([
                            'parecer' => $parecer,
                        ]);

                        $this->serviceBoletim($turmaId, $alunoId)->addParecer($parecerDescritivo);
                        $this->trySaveServiceBoletim($turmaId, $alunoId);
                    }
                }
            }

            $this->messenger->append('Pareceres postados com sucesso!', 'success');
        }
    }

    protected function atualizaNotaNecessariaExame($turmaId, $alunoId, $componenteCurricularId)
    {
        $notaExame = urldecode($this->serviceBoletim($turmaId, $alunoId)->preverNotaRecuperacao($componenteCurricularId));
        $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

        $situacaoComponente = $this->serviceBoletim($turmaId, $alunoId)
            ->getSituacaoComponentesCurriculares()
            ->componentesCurriculares[$componenteCurricularId]
            ->situacao;

        $situacaoEmExame = ($situacaoComponente == App_Model_MatriculaSituacao::EM_EXAME ||
            $situacaoComponente == App_Model_MatriculaSituacao::APROVADO_APOS_EXAME ||
            $situacaoComponente == App_Model_MatriculaSituacao::REPROVADO);

        if (!empty($notaExame) && $situacaoEmExame) {
            $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId, $notaExame);
            $obj->existe() ? $obj->edita() : $obj->cadastra();
        } else {
            $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId);
            $obj->excluir();
        }
    }

    protected function validateMatricula($matriculaId)
    {
        $ativo = false;

        if (!empty($matriculaId)) {
            $sql = 'SELECT m.ativo as ativo
                FROM pmieducar.matricula m
               WHERE m.cod_matricula = $1
               LIMIT 1';

            $ativo = $this->fetchPreparedQuery($sql, [$matriculaId], true, 'first-field');
        }

        return $ativo;
    }

    private function truncate($val, $f = '0')
    {
        if (($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }

        return $val;
    }

    public function removeHtmlTags(string $text = ''): string
    {
        return (new RemoveHtmlTagsStringService())->execute($text);
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'notas')) {
            $this->appendResponse($this->postNotas());
        } elseif ($this->isRequestFor('post', 'recuperacoes')) {
            $this->appendResponse($this->postRecuperacoes());
        } elseif ($this->isRequestFor('post', 'faltas-por-componente')) {
            $this->appendResponse($this->postFaltasPorComponente());
        } elseif ($this->isRequestFor('post', 'faltas-geral')) {
            $this->appendResponse($this->postFaltasGeral());
        } elseif ($this->isRequestFor('post', 'pareceres-por-etapa-e-componente')) {
            $this->appendResponse($this->postPareceresPorEtapaComponente());
        } elseif ($this->isRequestFor('post', 'pareceres-por-etapa-geral')) {
            $this->appendResponse($this->postPareceresPorEtapaGeral());
        } elseif ($this->isRequestFor('post', 'pareceres-anual-por-componente')) {
            $this->appendResponse($this->postPareceresAnualPorComponente());
        } elseif ($this->isRequestFor('post', 'pareceres-anual-geral')) {
            $this->appendResponse($this->postPareceresAnualGeral());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
