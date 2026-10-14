<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostPareceresPorEtapaComponenteRequest;
use App\Services\RemoveHtmlTagsStringService;
use iEducar\Modules\EvaluationRules\Exceptions\DisciplineNotExistsForSchoolClass;
use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotAllowDescriptiveOpinion;
use Illuminate\Http\Response;

class PareceresPorEtapaComponenteController extends DiarioController
{
    public function __invoke(PostPareceresPorEtapaComponenteRequest $request, RemoveHtmlTagsStringService $removeHtmlTags)
    {
        $alunoId = $request->integer('aluno_id');
        $turmaId = $request->integer('turma_id');
        $componenteCurricularId = $request->integer('componente_id');
        $etapa = $request->input('etapa');
        $parecer = (string) $request->input('parecer');

        $componentesTurma = $this->service->getComponentesTurma($turmaId);
        $matricula = $this->service->findMatricula($turmaId, $alunoId);
        $regra = $this->service->getRegraAvaliacaoPorMatricula($matricula->getKey());

        try {
            if ($regra->get('parecerDescritivo') != \RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE) {
                throw new EvaluationRuleNotAllowDescriptiveOpinion($turmaId, 'por etapa e componente');
            }

            if (!$this->service->validateComponenteTurma($componenteCurricularId, $componentesTurma, $turmaId, $matricula)) {
                throw new DisciplineNotExistsForSchoolClass($componenteCurricularId, $turmaId);
            }

            $parecerDescritivo = new \Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => $componenteCurricularId,
                'parecer' => $removeHtmlTags->execute($parecer),
                'etapa' => $etapa,
            ]);

            $boletim = $this->service->getServiceBoletim($turmaId, $alunoId, $matricula);
            $boletim->addParecer($parecerDescritivo);

            try {
                $boletim->save();
            } catch (\CoreExt_Service_Exception) {
                // Evita Exception ao não promover matrículas, pois não houve mudança de situação
            }
        } catch (\RuntimeException|\CoreExt_Exception_InvalidArgumentException|DisciplineNotExistsForSchoolClass|EvaluationRuleNotAllowDescriptiveOpinion $exception) {
            return $this->unprocessableResponse($exception);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Não foi possível salvar o parecer para o aluno e turma informados. (' . $exception->getMessage() . ')',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Parecer salvo com sucesso.',
        ], Response::HTTP_CREATED);
    }
}
