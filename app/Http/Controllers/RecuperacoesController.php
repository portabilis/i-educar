<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRecuperacoesRequest;
use iEducar\Modules\EvaluationRules\Exceptions\DisciplineNotExistsForSchoolClass;
use Illuminate\Http\Response;

class RecuperacoesController extends DiarioController
{
    public function __invoke(PostRecuperacoesRequest $request)
    {
        $alunoId = $request->integer('aluno_id');
        $turmaId = $request->integer('turma_id');
        $componenteCurricularId = $request->integer('componente_id');
        $etapa = $request->integer('etapa');
        $notaRecuperacao = $request->input('recuperacao');

        $componentesTurma = $this->service->getComponentesTurma($turmaId);
        $matricula = $this->service->findMatricula($turmaId, $alunoId);
        $regra = $this->service->getRegraAvaliacaoPorMatricula($matricula->getKey());

        try {
            if (!$this->service->validateComponenteTurma($componenteCurricularId, $componentesTurma, $turmaId, $matricula)) {
                throw new DisciplineNotExistsForSchoolClass($componenteCurricularId, $turmaId);
            }

            $nomeCampoRecuperacao = $this->service->getCampoTipoRecuperacao($matricula->getKey(), $regra);

            if (empty($nomeCampoRecuperacao)) {
                throw new \RuntimeException('A regra de avaliação da matrícula não utiliza recuperação paralela.');
            }

            $notaMaximaPermitida = $regra->getNotaMaximaRecuperacao($etapa);

            if (empty($notaMaximaPermitida)) {
                throw new \RuntimeException('A nota máxima para recuperação não foi definida.');
            }

            if ($notaRecuperacao > $notaMaximaPermitida) {
                throw new \RuntimeException(
                    "A nota de recuperação {$notaRecuperacao} está acima da configurada para nota máxima de recuperação que é {$notaMaximaPermitida}."
                );
            }

            $boletim = $this->service->getServiceBoletim($turmaId, $alunoId, $matricula);

            // Sem a nota da etapa, mantém a já lançada, como no resource legado.
            $notaOriginal = $request->input('nota')
                ?? $boletim->getNotaComponente($componenteCurricularId, $etapa)?->notaOriginal
                ?? 0.0;

            $valorNota = $boletim->calculateStageScore($etapa, $notaOriginal, $notaRecuperacao);

            $nota = new \Avaliacao_Model_NotaComponente([
                'componenteCurricular' => $componenteCurricularId,
                'nota' => $valorNota,
                'etapa' => $etapa,
                'notaOriginal' => $this->service->truncate($notaOriginal, 4),
                $nomeCampoRecuperacao => $notaRecuperacao,
            ]);

            $boletim->addNota($nota);

            try {
                $boletim->save();
            } catch (\CoreExt_Service_Exception) {
                // Evita Exception ao não promover matrículas, pois não houve mudança de situação
            }
        } catch (\RuntimeException|\CoreExt_Exception_InvalidArgumentException|DisciplineNotExistsForSchoolClass $exception) {
            return $this->unprocessableResponse($exception);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Não foi possível salvar a recuperação para o aluno e turma informados. (' . $exception->getMessage() . ')',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Recuperação salva com sucesso.',
        ], Response::HTTP_CREATED);
    }
}
