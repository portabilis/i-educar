<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFaltaComponenteRequest;
use iEducar\Modules\EvaluationRules\Exceptions\DisciplineNotExistsForSchoolClass;
use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotAllowDisciplineAbsence;
use iEducar\Modules\EvaluationRules\Exceptions\KnowledgeAreaWithoutGroupedDisciplines;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class FaltasComponenteController extends DiarioController
{
    public function __invoke(PostFaltaComponenteRequest $request)
    {
        $etapa = $request->integer('etapa');
        $faltas = $request->integer('faltas');
        $alunoId = $request->integer('aluno_id');
        $turmaId = $request->integer('turma_id');
        $componenteId = $request->integer('componente_id');
        $areaDoConhecimentoId = $request->integer('area_do_conhecimento_id');

        $componentesTurma = $this->service->getComponentesTurma($turmaId);
        $matricula = $this->service->findMatricula($turmaId, $alunoId);

        $regra = $this->service->getRegraAvaliacaoPorMatricula($matricula->getKey());

        try {
            if ($regra->get('tipoPresenca') != \RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
                throw new EvaluationRuleNotAllowDisciplineAbsence($turmaId);
            }

            $faltasAlunoTurma = [
                $componenteId => $faltas,
            ];

            if (!empty($areaDoConhecimentoId)) {
                $faltasAlunoTurma = $this->getFaltasPorComponentes(
                    $componentesTurma,
                    $matricula->ref_ref_cod_serie,
                    $areaDoConhecimentoId,
                    $faltas
                );
            }

            foreach ($faltasAlunoTurma as $componenteCurricularId => $faltaTurmaAlunoDisciplina) {
                if (!$this->service->validateComponenteTurma($componenteCurricularId, $componentesTurma, $turmaId, $matricula)) {
                    throw new DisciplineNotExistsForSchoolClass($componenteCurricularId, $turmaId);
                }
                $falta = new \Avaliacao_Model_FaltaComponente([
                    'componenteCurricular' => $componenteCurricularId,
                    'quantidade' => $faltaTurmaAlunoDisciplina,
                    'etapa' => $etapa,
                ]);
                $boletim = $this->service->getServiceBoletim($turmaId, $alunoId, $matricula);
                $boletim->addFalta($falta);
                $boletim->saveFaltas();

                try {
                    $boletim->promover();
                } catch (\CoreExt_Service_Exception) {
                    // Evita Exception ao não promover matrículas pois não houve mudança de situação
                }
            }
        } catch (\RuntimeException|\CoreExt_Exception_InvalidArgumentException|EvaluationRuleNotAllowDisciplineAbsence|DisciplineNotExistsForSchoolClass|KnowledgeAreaWithoutGroupedDisciplines $exception) {
            return $this->unprocessableResponse($exception);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Não foi possível salvar a falta por componente para o aluno e turma informados. (' . $exception->getMessage() . ')',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Falta por componente salva com sucesso.',
        ], Response::HTTP_CREATED);
    }

    private function getFaltasPorComponentes(
        Collection $componentesTurma,
        int $serieId,
        int $areaDoConhecimentoId,
        int $faltas,
    ): array {
        $componentesArea = $componentesTurma->when($serieId, function (Collection $collection, int $serieId) {
            return $collection->where('cod_serie', $serieId);
        })->where('knowledgeArea.id', $areaDoConhecimentoId)
            ->where('knowledgeArea.agrupar_descritores', true);

        if ($componentesArea->isEmpty()) {
            throw new KnowledgeAreaWithoutGroupedDisciplines($areaDoConhecimentoId);
        }

        $componenteAreaPrimeiro = $componentesArea->shift();
        // coloca a falta no primeiro componente do agrupamento
        $novoFaltaTurmaAluno[$componenteAreaPrimeiro->id] = $faltas;
        // coloca zero no restante dos componentes do agrupamento
        foreach ($componentesArea as $componenteArea) {
            $novoFaltaTurmaAluno[$componenteArea->id] = 0;
        }

        return $novoFaltaTurmaAluno;
    }
}
