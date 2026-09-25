<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostNotasRequest;
use iEducar\Modules\EvaluationRules\Exceptions\DisciplineNotExistsForSchoolClass;
use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use Illuminate\Http\Response;

class NotasController extends DiarioController
{
    public function __invoke(PostNotasRequest $request)
    {
        $alunoId = $request->integer('aluno_id');
        $turmaId = $request->integer('turma_id');
        $componenteCurricularId = $request->integer('componente_id');

        $componentesTurma = $this->service->getComponentesTurma($turmaId);
        $matricula = $this->service->findMatricula($turmaId, $alunoId);
        $regra = $this->service->getRegraAvaliacaoPorMatricula($matricula->getKey());

        try {
            if (!$this->service->validateComponenteTurma($componenteCurricularId, $componentesTurma, $turmaId, $matricula)) {
                throw new DisciplineNotExistsForSchoolClass($componenteCurricularId, $turmaId);
            }
            $boletim = $this->service->getServiceBoletim($turmaId, $alunoId, $matricula);

            $etapa = $request->input('etapa');
            $nota = $request->input('nota');
            $notaRecuperacao = $request->input('recuperacao');

            $nomeCampoRecuperacao = $this->service->getCampoTipoRecuperacao($matricula->getKey(), $regra);
            $notaOriginal = $this->service->truncate($nota, 4);
            $valorNota = $boletim->calculateStageScore($etapa, $notaOriginal, $notaRecuperacao);

            $notaValidacao = $notaOriginal;
            if (is_numeric($valorNota)) {
                $notaValidacao = $valorNota;
            }

            $this->validateScoreByEvaluationRule(
                $notaValidacao,
                $valorNota,
                $etapa,
                $regra,
                $boletim->getEvaluationRule()->isSumScoreCalculation()
            );

            $array_nota = [
                'componenteCurricular' => $componenteCurricularId,
                'nota' => $valorNota,
                'etapa' => $etapa,
                'notaOriginal' => $notaOriginal,
            ];

            if (!empty($nomeCampoRecuperacao)) {
                $array_nota[$nomeCampoRecuperacao] = $notaRecuperacao;
            }

            $nota = new \Avaliacao_Model_NotaComponente($array_nota);

            $boletim->verificaNotasLancadasNasEtapasAnteriores(
                $etapa,
                $componenteCurricularId
            );

            $boletim->addNota($nota);

            try {
                $boletim->save();
            } catch (\CoreExt_Service_Exception) {
                // Evita Exception ao não promover matrículas, pois não houve mudança de situação
            }

            $this->service->atualizaNotaNecessariaExame($turmaId, $alunoId, $componenteCurricularId, $matricula);
        } catch (\RuntimeException|DisciplineNotExistsForSchoolClass|MissingStagesException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Não foi possível salvar as notas para o aluno e turma informados. (' . $exception->getMessage() . ')',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Nota salva com sucesso.',
        ], Response::HTTP_CREATED);
    }

    private function validateScoreByEvaluationRule($notaValidacao, $valorNota, $etapa, $regra, bool $isSumScoreCalculation)
    {
        if ($etapa === 'Rc' && $notaValidacao > $regra->notaMaximaExameFinal) {
            throw new \RuntimeException(
                "A nota {$valorNota} está acima da configurada para nota máxima para exame que é {$regra->notaMaximaExameFinal}."
            );
        }

        if ($etapa !== 'Rc' && $notaValidacao > $regra->notaMaximaGeral && !$isSumScoreCalculation) {
            throw new \RuntimeException(
                "A nota {$valorNota} está acima da configurada para nota máxima geral que é {$regra->notaMaximaGeral}."
            );
        }

        if ($notaValidacao < $regra->notaMinimaGeral) {
            throw new \RuntimeException(
                "A nota {$valorNota} está abaixo da configurada para nota mínima geral que é {$regra->notaMinimaGeral}."
            );
        }
    }
}
