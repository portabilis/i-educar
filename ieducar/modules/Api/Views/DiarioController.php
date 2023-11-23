<?php

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\RegistrationStatus;
use App\Models\View\Discipline;
use App\Services\RemoveHtmlTagsStringService;
use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotAllowGeneralAbsence;
use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use iEducar\Support\Exceptions\Error;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

    protected function validateComponenteTurma($componenteCurricularId, Collection $componentesTurma, LegacyRegistration $registration): bool
    {
        $turmaId = $componentesTurma->value('cod_turma');

        return Cache::remember('valid_component_' . $componenteCurricularId . '_schoolclass_' . $turmaId . '_grade_' . $registration->ref_ref_cod_serie, now()->addMinute(), function () use ($componenteCurricularId, $componentesTurma, $turmaId, $registration) {
            $valid = $componentesTurma->when($registration->ref_ref_cod_serie, function (Collection $collection, int $serieId) {
                return $collection->where('cod_serie', $serieId);
            })->contains($componenteCurricularId);

            //pula a mensagem se for area do conhecimento e um componente a turma
            if (!$valid && !$componentesTurma->contains($componenteCurricularId)) {
                $this->messenger->append("Componente curricular de código {$componenteCurricularId} não existe para a turma {$turmaId}.", 'error');
                $this->appendResponse('error', [
                    'code' => Error::DISCIPLINE_NOT_EXISTS_FOR_SCHOOL_CLASS,
                    'message' => "Componente curricular de código {$componenteCurricularId} não existe para a turma {$turmaId}.",
                ]);

                return false;
            }

            return $valid;
        });
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

    protected function findMatricula($turmaId, $alunoId)
    {
        return Cache::remember('matricula_id_' . $turmaId . '_' . $alunoId, now()->addMinute(), function () use ($turmaId, $alunoId) {
            return LegacyRegistration::query()
                ->active()
                ->whereHas('enrollments', function ($q) use ($turmaId) {
                    $q->where('ref_cod_turma', $turmaId);
                    $q->where(function ($q) {
                        $q->where('ativo', 1);
                        $q->orWhere('transferido', true);
                    });
                })
                ->whereStudent($alunoId)
                ->whereIn('aprovado', [
                    RegistrationStatus::APPROVED,
                    RegistrationStatus::REPROVED,
                    RegistrationStatus::ONGOING,
                    RegistrationStatus::TRANSFERRED,
                    RegistrationStatus::APPROVED_BY_BOARD,
                    RegistrationStatus::APPROVED_WITH_DEPENDENCY,
                    RegistrationStatus::REPROVED_BY_ABSENCE,
                ])
                ->orderBy('aprovado')
                ->first([
                    'cod_matricula',
                    'ref_ref_cod_serie',
                    'ref_ref_cod_escola',
                    'ano',
                ]);
        });
    }

    /**
     * @param int $turmaId
     * @param int $alunoId
     * @return Avaliacao_Service_Boletim|bool
     *
     * @throws CoreExt_Exception
     */
    protected function serviceBoletim($turmaId, $alunoId)
    {
        $matricula = $this->findMatricula($turmaId, $alunoId);

        if ($matricula) {
            if (!isset($this->_boletimServiceInstances)) {
                $this->_boletimServiceInstances = [];
            }

            // set service
            if (!isset($this->_boletimServiceInstances[$matricula->cod_matricula])) {
                $params = ['matricula' => $matricula->cod_matricula];
                $this->_boletimServiceInstances[$matricula->cod_matricula] = new Avaliacao_Service_Boletim($params);
            }

            // validates service
            if (is_null($this->_boletimServiceInstances[$matricula->cod_matricula])) {
                throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matrícula {$matricula->cod_matricula}.");
            }

            return $this->_boletimServiceInstances[$matricula->cod_matricula];
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
        if (!$this->canPostNotas()) {
            return false;
        }

        $etapa = $this->getRequest()->etapa;
        $notas = $this->getRequest()->notas;

        foreach ($notas as $turmaId => $notaTurma) {
            $componentesTurma = $this->getComponentesTurma($turmaId);

            foreach ($notaTurma as $alunoId => $notaTurmaAluno) {
                $matricula = $this->findMatricula($turmaId, $alunoId);

                if (empty($matricula)) {
                    continue;
                }

                foreach ($notaTurmaAluno as $componenteCurricularId => $notaTurmaAlunoDisciplina) {
                    if (!$this->validateComponenteTurma($componenteCurricularId, $componentesTurma, $matricula)) {
                        continue;
                    }

                    if (!$serviceBoletim = $this->serviceBoletim($turmaId, $alunoId)) {
                        continue;
                    }

                    $regra = $serviceBoletim->getEvaluationRule();

                    $notaOriginal = $notaTurmaAlunoDisciplina['nota'];
                    $notaRecuperacao = $notaTurmaAlunoDisciplina['recuperacao'];
                    $nomeCampoRecuperacao = $this->defineCampoTipoRecuperacao($matricula->cod_matricula);
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
                        'notaOriginal' => $notaOriginal,
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
                $componentesTurma = $this->getComponentesTurma($turmaId);

                foreach ($notaTurma as $alunoId => $notaTurmaAluno) {
                    $matricula = $this->findMatricula($turmaId, $alunoId);

                    if (!$matricula) {
                        continue;
                    }

                    foreach ($notaTurmaAluno as $componenteCurricularId => $notaTurmaAlunoDisciplina) {
                        if ($this->validateComponenteTurma($componenteCurricularId, $componentesTurma, $matricula)) {
                            $notaOriginal = $notaTurmaAlunoDisciplina['nota'];

                            if (is_null($notaOriginal)) {
                                $notaOriginalPersistida = $this->serviceBoletim($turmaId, $alunoId)->getNotaComponente($componenteCurricularId, $etapa)->notaOriginal;

                                if (is_null($notaOriginalPersistida)) {
                                    $notaOriginal = 0.0;
                                } else {
                                    $notaOriginal = $notaOriginalPersistida;
                                }
                            }

                            if (!$serviceBoletim = $this->serviceBoletim($turmaId, $alunoId)) {
                                continue;
                            }

                            $regra = $serviceBoletim->getRegra();

                            $notaRecuperacao = $notaTurmaAlunoDisciplina['recuperacao'];
                            $nomeCampoRecuperacao = $this->defineCampoTipoRecuperacao($matricula->cod_matricula);

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
                                $nomeCampoRecuperacao => $notaRecuperacao,
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

        return match ((int) $regra->get('tipoRecuperacaoParalela')) {
            RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA => 'notaRecuperacaoParalela',
            RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS => 'notaRecuperacaoEspecifica',
            default => '',
        };
    }

    protected function getComponentesTurma(int $turmaId): Collection
    {
        $disciplinaDispensada = LegacySchoolClass::query()->whereKey($turmaId)->value('ref_cod_disciplina_dispensada');

        return Discipline::query()
            ->where('cod_turma', $turmaId)
            ->when($disciplinaDispensada, function ($q, $disciplinaDispensada) {
                $q->where('id', '<>', $disciplinaDispensada);
            })
            ->with('knowledgeArea:id,agrupar_descritores')
            ->orderBy('nome')
            ->get([
                'id',
                'cod_turma',
                'cod_serie',
                'area_conhecimento_id',
            ]);
    }

    protected function postFaltasPorComponente()
    {
        if ($this->canPostFaltasPorComponente()) {
            $etapa = $this->getRequest()->etapa;
            $faltas = $this->getRequest()->faltas;

            foreach ($faltas as $turmaId => $faltaTurma) {
                $componentesTurma = $this->getComponentesTurma($turmaId);

                foreach ($faltaTurma as $alunoId => $faltaTurmaAluno) {
                    $matricula = $this->findMatricula($turmaId, $alunoId);

                    if (!$matricula) {
                        continue;
                    }

                    if ($this->getRegra($matricula->cod_matricula)->get('tipoPresenca') != RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
                        $this->messenger->append("A regra da turma $turmaId não permite lançamento de faltas por componente.", 'error');
                        $this->appendResponse('error', [
                            'code' => Error::SCHOOL_CLASS_DOESNT_ALOW_FREQUENCY_BY_DISCIPLINE,
                            'message' => "A regra da turma $turmaId não permite lançamento de faltas por componente.",
                        ]);

                        return false;
                    }

                    $faltaTurmaAluno = $this->mergeComponenteArea($faltaTurmaAluno, $componentesTurma, $matricula->ref_ref_cod_serie);

                    foreach ($faltaTurmaAluno as $componenteCurricularId => $faltaTurmaAlunoDisciplina) {
                        if ($this->validateComponenteTurma($componenteCurricularId, $componentesTurma, $matricula)) {
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

            $this->messenger->append('Faltas postadas com sucesso!', 'success');
        }
    }

    private function mergeComponenteArea(array $faltaTurmaAluno, Collection $componentesTurma, ?int $serieId): array
    {
        $novoFaltaTurmaAluno = [];

        foreach ($faltaTurmaAluno as $componenteCurricularId => $faltaTurmaAlunoDisciplina) {
            $areaDoConhecimento = $faltaTurmaAlunoDisciplina['area_do_conhecimento'] ?? null;
            if ($areaDoConhecimento) {
                $componentesArea = $componentesTurma->when($serieId, function (Collection $collection, int $serieId) {
                    return $collection->where('cod_serie', $serieId);
                })->where('knowledgeArea.id', $areaDoConhecimento)
                    ->where('knowledgeArea.agrupar_descritores', true);

                if ($componentesArea->isNotEmpty()) {
                    $componenteAreaPrimeiro = $componentesArea->shift();
                    //coloca a falta no primeiro componente do agrupamento
                    $novoFaltaTurmaAluno[$componenteAreaPrimeiro->id] = $faltaTurmaAlunoDisciplina;
                    //coloca zero no restante dos componentes do agrupamento
                    foreach ($componentesArea as $componenteArea) {
                        $novoFaltaTurmaAluno[$componenteArea->id] = [
                            'valor' => 0,
                        ];
                    }
                }
            } else {
                $novoFaltaTurmaAluno[$componenteCurricularId] = $faltaTurmaAlunoDisciplina;
            }
        }

        return $novoFaltaTurmaAluno;
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
                    $matricula = $this->findMatricula($turmaId, $alunoId);

                    if (empty($matricula)) {
                        continue;
                    }

                    if ($this->getRegra($matricula->cod_matricula)->get('tipoPresenca') != RegraAvaliacao_Model_TipoPresenca::GERAL) {
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
                $componentesTurma = $this->getComponentesTurma($turmaId);

                foreach ($parecerTurma as $alunoId => $parecerTurmaAluno) {
                    $matricula = $this->findMatricula($turmaId, $alunoId);

                    if (!empty($matricula)) {
                        if ($this->getRegra($matricula->cod_matricula)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE) {
                            throw new CoreExt_Exception("A regra da turma {$turmaId} não permite lançamento de pareceres por etapa e componente.");
                        }

                        foreach ($parecerTurmaAluno as $componenteCurricularId => $parecerTurmaAlunoComponente) {
                            if ($this->validateComponenteTurma($componenteCurricularId, $componentesTurma, $matricula)) {

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
                    $matricula = $this->findMatricula($turmaId, $alunoId);

                    if (!empty($matricula)) {
                        if ($this->getRegra($matricula->cod_matricula)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
                            throw new CoreExt_Exception("A regra da turma {$turmaId} não permite lançamento de pareceres anual por componente.");
                        }

                        foreach ($parecerTurmaAluno as $componenteCurricularId => $parecerTurmaAlunoComponente) {
                            if ($this->validateComponenteCurricular($matricula->cod_matricula, $componenteCurricularId)) {

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
                    $matricula = $this->findMatricula($turmaId, $alunoId);

                    if (!empty($matricula)) {
                        if ($this->getRegra($matricula->cod_matricula)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL) {
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
                    $matricula = $this->findMatricula($turmaId, $alunoId);

                    if (!empty($matricula)) {
                        if ($this->getRegra($matricula->cod_matricula)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL) {
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
        $matricula = $this->findMatricula($turmaId, $alunoId);

        $situacaoComponente = $this->serviceBoletim($turmaId, $alunoId)
            ->getSituacaoComponentesCurriculares()
            ->componentesCurriculares[$componenteCurricularId]
            ->situacao;

        $situacaoEmExame = ($situacaoComponente == App_Model_MatriculaSituacao::EM_EXAME ||
            $situacaoComponente == App_Model_MatriculaSituacao::APROVADO_APOS_EXAME ||
            $situacaoComponente == App_Model_MatriculaSituacao::REPROVADO);

        if (!empty($notaExame) && $situacaoEmExame) {
            $obj = new clsModulesNotaExame($matricula->cod_matricula, $componenteCurricularId, $notaExame);
            $obj->existe() ? $obj->edita() : $obj->cadastra();
        } else {
            $obj = new clsModulesNotaExame($matricula->cod_matricula, $componenteCurricularId);
            $obj->excluir();
        }
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
