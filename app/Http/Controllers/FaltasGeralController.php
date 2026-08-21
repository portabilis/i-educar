<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFaltaGeralRequest;
use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotAllowGeneralAbsence;

class FaltasGeralController extends DiarioController
{
    public function __invoke(PostFaltaGeralRequest $request)
    {
        $etapa = $request->integer('etapa');
        $faltas = $request->integer('faltas');
        $alunoId = $request->integer('aluno_id');
        $turmaId = $request->integer('turma_id');

        $matricula = $this->service->findMatricula($turmaId, $alunoId);

        if (empty($matricula)) {
            return response()->json([
                'message' => 'Matrícula não encontrada para o aluno e turma informados.',
            ]);
        }

        $regra = $this->service->getRegraAvaliacaoPorMatricula($matricula->getKey());

        try {
            if ($regra->get('tipoPresenca') != \RegraAvaliacao_Model_TipoPresenca::GERAL) {
                throw new EvaluationRuleNotAllowGeneralAbsence($turmaId);
            }

            $falta = new \Avaliacao_Model_FaltaGeral([
                'quantidade' => $faltas,
                'etapa' => $etapa,
            ]);

            $boletim = $this->service->getServiceBoletim($turmaId, $alunoId, $matricula);

            $boletim->addFalta($falta);
            $boletim->saveFaltas();
            $boletim->promover();
        } catch (EvaluationRuleNotAllowGeneralAbsence $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\CoreExt_Service_Exception) {
            // Evita Exception ao não promover matrículas pois não houve mudança de situação
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Não foi possível salvar as faltas gerais para o aluno e turma informados. (' . $exception->getMessage() . ')',
            ], 500);
        }

        return response()->json([
            'message' => 'Faltas gerais salvas com sucesso.',
        ], 202);
    }
}
