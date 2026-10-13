<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\RegistrationStatus;
use App\Models\View\Discipline;
use App_Model_MatriculaSituacao;
use iEducar\Modules\AcademicYear\Exceptions\DisciplineNotLinkedToRegistrationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DiarioControllerService
{
    public function findMatricula(int $turmaId, int $alunoId): ?LegacyRegistration
    {
        return Cache::remember('matricula_id_' . $turmaId . '_' . $alunoId, now()->addMinutes(5), function () use ($turmaId, $alunoId) {
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

    public function getMatricula(int $matriculaId): array
    {
        return \App_Model_IedFinder::getMatricula($matriculaId);
    }

    public function getRegraAvaliacaoPorMatricula(int $matriculaId)
    {
        return \App_Model_IedFinder::getRegraAvaliacaoPorMatricula($matriculaId);
    }

    public function getComponentesTurma(int $turmaId): Collection
    {
        return Cache::remember('componentes_turma_id_' . $turmaId, now()->addMinutes(5), function () use ($turmaId) {
            $disciplinaDispensada = LegacySchoolClass::query()
                ->whereKey($turmaId)->value('ref_cod_disciplina_dispensada');

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
        });
    }

    public function validateComponenteTurma(int $componenteCurricularId, Collection $componentesTurma, int $turmaId, LegacyRegistration $registration): bool
    {
        return Cache::remember('valid_component_' . $componenteCurricularId . '_schoolclass_' . $turmaId . '_grade_' . $registration->ref_ref_cod_serie, now()->addMinutes(5), function () use ($componenteCurricularId, $componentesTurma, $registration) {
            $valid = $componentesTurma->when($registration->ref_ref_cod_serie, function (Collection $collection, int $serieId) {
                return $collection->where('cod_serie', $serieId);
            })->contains($componenteCurricularId);

            // pula a mensagem se for area do conhecimento e um componente a turma
            if (!$valid && !$componentesTurma->contains($componenteCurricularId)) {
                return false;
            }

            return $valid;
        });
    }

    public function validateComponenteMatricula(int $componenteCurricularId, int $matriculaId): bool
    {
        $componentes = \App_Model_IedFinder::getComponentesPorMatricula($matriculaId);

        return in_array($componenteCurricularId, \CoreExt_Entity::entityFilterAttr($componentes, 'id', 'id'));
    }

    /**
     * A dispensa é por etapa: o componente dispensado numa etapa continua na matrícula nas demais.
     * O exame (Rc) não tem etapa própria e vale enquanto o componente estiver na matrícula em alguma
     * das etapas que cursa: as específicas do componente ou, sem elas, as do calendário.
     */
    public function validateComponenteMatriculaEtapa(int $componenteCurricularId, int $matriculaId, int $turmaId, string $etapa, int $quantidadeEtapas): bool
    {
        if (is_numeric($etapa)) {
            return $this->isComponenteNaMatriculaNaEtapa($componenteCurricularId, $matriculaId, $turmaId, (int) $etapa);
        }

        foreach ($this->getEtapasCursadas($componenteCurricularId, $turmaId, $quantidadeEtapas) as $etapaComponente) {
            if ($this->isComponenteNaMatriculaNaEtapa($componenteCurricularId, $matriculaId, $turmaId, (int) $etapaComponente)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Na etapa numérica, a etapa está entre as dispensadas; no exame (Rc), todas as etapas que o componente cursa estão.
     */
    public function isAlunoDispensadoNaEtapa(int $componenteCurricularId, LegacyRegistration $registration, int $turmaId, string $etapa, int $quantidadeEtapas): bool
    {
        $etapasDispensadas = \App_Model_IedFinder::validaDispensaPorMatricula(
            $registration->getKey(),
            $registration->ref_ref_cod_serie,
            $registration->ref_ref_cod_escola,
            $componenteCurricularId
        );

        if (is_numeric($etapa)) {
            return in_array((int) $etapa, $etapasDispensadas);
        }

        $etapasCursadas = $this->getEtapasCursadas($componenteCurricularId, $turmaId, $quantidadeEtapas);

        return $etapasCursadas && !array_diff($etapasCursadas, $etapasDispensadas);
    }

    /**
     * Etapas específicas do componente ou, sem elas, as do calendário.
     */
    private function getEtapasCursadas(int $componenteCurricularId, int $turmaId, int $quantidadeEtapas): array
    {
        $etapasComponente = \App_Model_IedFinder::getEtapasComponente($turmaId, $componenteCurricularId);

        if ($etapasComponente) {
            return $etapasComponente;
        }

        return $quantidadeEtapas > 0 ? range(1, $quantidadeEtapas) : [];
    }

    private function isComponenteNaMatriculaNaEtapa(int $componenteCurricularId, int $matriculaId, int $turmaId, int $etapa): bool
    {
        try {
            $componentes = \App_Model_IedFinder::getComponentesPorMatricula(
                codMatricula: $matriculaId,
                componenteCurricularId: $componenteCurricularId,
                etapa: $etapa,
                turma: $turmaId,
            );
        } catch (DisciplineNotLinkedToRegistrationException) {
            // Sem vínculo na turma, a série só devolve o componente nas etapas específicas dele.
            return false;
        }

        return array_key_exists($componenteCurricularId, $componentes);
    }

    public function getServiceBoletim(int $turmaId, int $alunoId, ?LegacyRegistration $registration): \Avaliacao_Service_Boletim
    {
        if (is_null($registration)) {
            $registration = $this->findMatricula($turmaId, $alunoId);
        }

        $params = [
            'matricula' => $registration->getKey(),
        ];

        $boletim = new \Avaliacao_Service_Boletim($params);

        // Inalcançável: `new` nunca devolve nulo.
        if (is_null($boletim)) {
            // @codeCoverageIgnoreStart
            throw new \CoreExt_Exception("Não foi possível instanciar o serviço boletim para a matrícula {$registration->getKey()}.");
            // @codeCoverageIgnoreEnd
        }

        return $boletim;
    }

    public function getCampoTipoRecuperacao(int $matriculaId, $regra = null): string
    {
        if (is_null($regra)) {
            $regra = $this->getRegraAvaliacaoPorMatricula($matriculaId);
        }

        return match ((int) $regra->get('tipoRecuperacaoParalela')) {
            \RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA => 'notaRecuperacaoParalela',
            \RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS => 'notaRecuperacaoEspecifica',
            default => '',
        };
    }

    public function truncate($val, $f = '0')
    {
        if (($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }

        return $val;
    }

    public function atualizaNotaNecessariaExame(
        int $turmaId,
        int $alunoId,
        int $componenteCurricularId,
        ?LegacyRegistration $registration = null
    ) {
        $boletim = $this->getServiceBoletim($turmaId, $alunoId, $registration);
        $notaExame = urldecode($boletim->preverNotaRecuperacao($componenteCurricularId));

        if (is_null($registration)) {
            $registration = $this->findMatricula($turmaId, $alunoId);
        }

        // A lista de situações não traz componente dispensado nem, em dependência, fora dela.
        $situacaoComponente = ($boletim->getSituacaoComponentesCurriculares()
            ->componentesCurriculares[$componenteCurricularId] ?? null)?->situacao;

        $situacaoEmExame = ($situacaoComponente == App_Model_MatriculaSituacao::EM_EXAME ||
            $situacaoComponente == App_Model_MatriculaSituacao::APROVADO_APOS_EXAME ||
            $situacaoComponente == App_Model_MatriculaSituacao::REPROVADO);

        if (!empty($notaExame) && $situacaoEmExame) {
            if (is_numeric($registration->getKey()) && is_numeric($componenteCurricularId) && is_numeric($notaExame)) {
                DB::table('modules.nota_exame')->updateOrInsert(
                    ['ref_cod_matricula' => $registration->getKey(), 'ref_cod_componente_curricular' => $componenteCurricularId],
                    ['nota_exame' => $notaExame]
                );
            }
        } elseif (is_numeric($registration->getKey()) && is_numeric($componenteCurricularId)) {
            DB::table('modules.nota_exame')
                ->where('ref_cod_matricula', $registration->getKey())
                ->where('ref_cod_componente_curricular', $componenteCurricularId)
                ->delete();
        }
    }
}
